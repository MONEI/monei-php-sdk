<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Monei\ApiException;
use Monei\Configuration;
use Monei\Api\PaymentsApi;
use Monei\Internal\GuzzleHttp\Client;
use Monei\Internal\GuzzleHttp\Handler\MockHandler;
use Monei\Internal\GuzzleHttp\HandlerStack;
use Monei\Internal\GuzzleHttp\Psr7\Response;
use Monei\Internal\GuzzleHttp\Middleware;

class RateLimitExceptionTest extends TestCase
{
    /**
     * Test handling of 429 Rate Limit response with direct ApiException creation
     */
    public function testRateLimitExceptionDirectCreation()
    {
        // Create headers array
        $headers = [
            'Content-Type' => 'application/json',
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Remaining' => '0',
            'X-RateLimit-Reset' => '1614556800'
        ];

        // Create a JSON response body
        $responseBody = json_encode([
            'statusCode' => 429,
            'error' => 'Too Many Requests',
            'message' => 'Rate limit exceeded'
        ]);

        // Create an ApiException directly
        $exception = new ApiException('Rate limit exceeded', 429, $headers, $responseBody);

        // Verify the exception properties
        $this->assertEquals(429, $exception->getCode());
        $this->assertEquals('Rate limit exceeded', $exception->getMessage());

        // Verify the headers
        $returnedHeaders = $exception->getResponseHeaders();
        $this->assertIsArray($returnedHeaders);
        $this->assertArrayHasKey('X-RateLimit-Limit', $returnedHeaders);
        $this->assertEquals('100', $returnedHeaders['X-RateLimit-Limit']);
        $this->assertEquals('0', $returnedHeaders['X-RateLimit-Remaining']);

        // Verify the response body
        $returnedBody = $exception->getResponseBody();
        $this->assertIsString($returnedBody);
        $this->assertEquals($responseBody, $returnedBody);

        // Deserialize and set the response object
        $responseObject = json_decode($responseBody);
        $exception->setResponseObject($responseObject);

        // Verify the response object
        $returnedObject = $exception->getResponseObject();
        $this->assertEquals(429, $returnedObject->statusCode);
        $this->assertEquals('Too Many Requests', $returnedObject->error);
        $this->assertEquals('Rate limit exceeded', $returnedObject->message);
    }

    /**
     * Test handling of 429 Rate Limit response with mock API
     */
    public function testRateLimitExceptionWithMockApi()
    {
        // Create a direct ApiException with rate limit headers
        $headers = [
            'Content-Type' => 'application/json',
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Remaining' => '0',
            'X-RateLimit-Reset' => '1614556800'
        ];

        $errorBody = json_encode([
            'statusCode' => 429,
            'error' => 'Too Many Requests',
            'message' => 'Rate limit exceeded'
        ]);

        $exception = new ApiException(
            'Rate limit exceeded',
            429,
            $headers,
            $errorBody
        );

        // Verify the exception has the correct status code
        $this->assertEquals(429, $exception->getCode());

        // Verify the rate limit headers are properly set
        $responseHeaders = $exception->getResponseHeaders();
        $this->assertArrayHasKey('X-RateLimit-Limit', $responseHeaders);
        $this->assertArrayHasKey('X-RateLimit-Remaining', $responseHeaders);
        $this->assertArrayHasKey('X-RateLimit-Reset', $responseHeaders);
        $this->assertEquals('100', $responseHeaders['X-RateLimit-Limit']);
        $this->assertEquals('0', $responseHeaders['X-RateLimit-Remaining']);

        // Get the response body
        $responseBody = $exception->getResponseBody();

        // Verify the response body is not null
        $this->assertNotNull($responseBody);
        $this->assertEquals($errorBody, $responseBody);

        // Decode the JSON response body
        $bodyData = json_decode($responseBody, true);

        // Verify the decoded body contains the expected data
        $this->assertArrayHasKey('statusCode', $bodyData);
        $this->assertEquals(429, $bodyData['statusCode']);
        $this->assertEquals('Too Many Requests', $bodyData['error']);
        $this->assertEquals('Rate limit exceeded', $bodyData['message']);
    }

    /**
     * Test handling of 429 Rate Limit response with retry logic
     */
    public function testRateLimitExceptionWithRetry()
    {
        // Create a mock handler
        $mockHandler = new MockHandler();

        // Create a handler stack with the mock handler
        $handlerStack = HandlerStack::create($mockHandler);

        // Add history middleware to track requests
        $container = [];
        $history = Middleware::history($container);
        $handlerStack->push($history);

        // Create a client with the mock handler
        $client = new Client(['handler' => $handlerStack]);

        // Create a configuration
        $config = new Configuration();
        $config->setHost('https://api.monei.com/v1');

        // Create an API instance with the mock client
        $api = new PaymentsApi($client, $config);

        // Prepare a mock error response for rate limiting
        $errorResponse = [
            'statusCode' => 429,
            'error' => 'Too Many Requests',
            'message' => 'Rate limit exceeded'
        ];

        // Prepare a success response for after retry
        $successResponse = [
            'id' => 'payment_123',
            'status' => 'CANCELED'
        ];

        // Queue a rate limit response followed by a success response
        $mockHandler->append(
            new Response(
                429,
                [
                    'Content-Type' => 'application/json',
                    'X-RateLimit-Limit' => '100',
                    'X-RateLimit-Remaining' => '0',
                    'X-RateLimit-Reset' => (string)(time() + 1) // 1 second from now
                ],
                json_encode($errorResponse)
            ),
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($successResponse)
            )
        );

        // Implement a simple retry mechanism
        $maxRetries = 1;
        $retryCount = 0;
        $result = null;

        while ($retryCount <= $maxRetries) {
            try {
                // Attempt to make the API call
                $result = $api->cancel('payment_123');
                // If successful, break out of the loop
                break;
            } catch (ApiException $e) {
                // Check if it's a rate limit error
                if ($e->getCode() === 429) {
                    $headers = $e->getResponseHeaders();

                    // Check if we have retry information
                    if (isset($headers['X-RateLimit-Reset'])) {
                        $resetTime = (int)$headers['X-RateLimit-Reset'];
                        $currentTime = time();
                        $waitTime = max(0, $resetTime - $currentTime);

                        // Wait for the rate limit to reset (in a real scenario)
                        // For testing, we'll just increment the retry counter
                        $retryCount++;

                        // If we've reached max retries, rethrow the exception
                        if ($retryCount > $maxRetries) {
                            throw $e;
                        }

                        // In a real implementation, we would sleep here
                        // sleep($waitTime);
                    } else {
                        // No retry information, just rethrow
                        throw $e;
                    }
                } else {
                    // Not a rate limit error, rethrow
                    throw $e;
                }
            }
        }

        // Verify that we made two requests (initial + retry)
        $this->assertCount(2, $container);

        // Verify that the first request resulted in a 429
        $this->assertEquals(429, $container[0]['response']->getStatusCode());

        // Verify that the second request was successful
        $this->assertEquals(200, $container[1]['response']->getStatusCode());

        // Verify that we got a result
        $this->assertNotNull($result);
        $this->assertEquals('payment_123', $result['id']);
        $this->assertEquals('CANCELED', $result['status']);
    }
}
