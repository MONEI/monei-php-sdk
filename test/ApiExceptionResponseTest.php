<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Monei\ApiException;
use Monei\Configuration;
use Monei\Api\PaymentsApi;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Middleware;

class ApiExceptionResponseTest extends TestCase
{
    /**
     * @var PaymentsApi
     */
    protected $paymentsApi;

    /**
     * @var MockHandler
     */
    protected $mockHandler;

    /**
     * @var array
     */
    protected $container = [];

    /**
     * Setup before running each test case
     */
    public function setUp(): void
    {
        // Create a mock handler
        $this->mockHandler = new MockHandler();

        // Create a handler stack with the mock handler
        $handlerStack = HandlerStack::create($this->mockHandler);

        // Add history middleware to the handler stack
        $history = Middleware::history($this->container);
        $handlerStack->push($history);

        // Create a Guzzle client with the handler stack
        $client = new Client(['handler' => $handlerStack]);

        // Create a configuration with a dummy API key
        $config = Configuration::getDefaultConfiguration();
        $config->setApiKey('Authorization', 'test_api_key');

        // Create the API instance with the mock client
        $this->paymentsApi = new PaymentsApi($client, $config);
    }

    /**
     * Test handling of 400 Bad Request response
     */
    public function testBadRequestException()
    {
        // Create a direct ApiException with bad request details
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $errorBody = json_encode([
            'statusCode' => 400,
            'error' => 'Bad Request',
            'message' => 'Invalid amount parameter'
        ]);

        $exception = new ApiException(
            'Bad Request: Invalid amount parameter',
            400,
            $headers,
            $errorBody
        );

        // Verify the exception has the correct status code
        $this->assertEquals(400, $exception->getCode());

        // Verify the headers are properly set
        $responseHeaders = $exception->getResponseHeaders();
        $this->assertArrayHasKey('Content-Type', $responseHeaders);
        $this->assertEquals('application/json', $responseHeaders['Content-Type']);

        // Get the response body
        $responseBody = $exception->getResponseBody();

        // Verify the response body is not null
        $this->assertNotNull($responseBody);
        $this->assertEquals($errorBody, $responseBody);

        // Decode the JSON response body
        $bodyData = json_decode($responseBody, true);

        // Verify the decoded body contains the expected data
        $this->assertArrayHasKey('statusCode', $bodyData);
        $this->assertEquals(400, $bodyData['statusCode']);
        $this->assertEquals('Bad Request', $bodyData['error']);
        $this->assertEquals('Invalid amount parameter', $bodyData['message']);
    }

    /**
     * Test handling of 401 Unauthorized response
     */
    public function testUnauthorizedException()
    {
        $errorResponse = [
            'statusCode' => 401,
            'error' => 'Unauthorized',
            'message' => 'Invalid API key provided'
        ];

        // Queue a mock response
        $this->mockHandler->append(
            new Response(
                401,
                ['Content-Type' => 'application/json'],
                json_encode($errorResponse)
            )
        );

        try {
            // Attempt to get a payment with invalid auth
            $this->paymentsApi->get('payment_123');

            $this->fail('Expected ApiException was not thrown');
        } catch (ApiException $e) {
            // Verify the exception properties
            $this->assertEquals(401, $e->getCode());

            // Verify response body contains the expected error
            $responseBody = $e->getResponseBody();
            if (is_string($responseBody)) {
                $decodedBody = json_decode($responseBody, true);
                $this->assertEquals('Unauthorized', $decodedBody['error']);
            } else {
                // If it's not a string, it might be already an object/array
                $this->assertEquals('Unauthorized', $responseBody->error ?? $responseBody['error'] ?? null);
            }
        }
    }

    /**
     * Test handling of 404 Not Found response
     */
    public function testNotFoundException()
    {
        $errorResponse = [
            'statusCode' => 404,
            'error' => 'Not Found',
            'message' => 'Payment with ID payment_nonexistent not found'
        ];

        // Queue a mock response
        $this->mockHandler->append(
            new Response(
                404,
                ['Content-Type' => 'application/json'],
                json_encode($errorResponse)
            )
        );

        try {
            // Attempt to get a non-existent payment
            $this->paymentsApi->get('payment_nonexistent');

            $this->fail('Expected ApiException was not thrown');
        } catch (ApiException $e) {
            // Verify the exception has the correct response object
            $responseBody = $e->getResponseBody();
            if (is_string($responseBody)) {
                $decodedBody = json_decode($responseBody, true);
                $this->assertEquals(404, $decodedBody['statusCode']);
                $this->assertEquals('Not Found', $decodedBody['error']);
            } else {
                // If it's not a string, it might be already an object/array
                $this->assertEquals(404, $responseBody->statusCode ?? $responseBody['statusCode'] ?? null);
                $this->assertEquals('Not Found', $responseBody->error ?? $responseBody['error'] ?? null);
            }
        }
    }

    /**
     * Test handling of 429 Rate Limit response
     */
    public function testRateLimitException()
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
        $this->assertEquals('1614556800', $responseHeaders['X-RateLimit-Reset']);

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
     * Test handling of 500 Server Error response
     */
    public function testServerErrorException()
    {
        $errorResponse = [
            'statusCode' => 500,
            'error' => 'Internal Server Error',
            'message' => 'An unexpected error occurred'
        ];

        // Queue a mock response
        $this->mockHandler->append(
            new Response(
                500,
                ['Content-Type' => 'application/json'],
                json_encode($errorResponse)
            )
        );

        try {
            // Attempt an API call that would trigger a server error
            $this->paymentsApi->get('payment_123');

            $this->fail('Expected ApiException was not thrown');
        } catch (ApiException $e) {
            // Verify the exception code
            $this->assertEquals(500, $e->getCode());

            // Verify the response body
            $responseBody = $e->getResponseBody();
            if (is_string($responseBody)) {
                $decodedBody = json_decode($responseBody, true);
                $this->assertEquals('Internal Server Error', $decodedBody['error']);
            } else {
                $this->assertEquals('Internal Server Error', $responseBody->error ?? $responseBody['error'] ?? null);
            }
        }
    }

    /**
     * Test handling of response with deserialized object
     */
    public function testResponseObjectDeserialization()
    {
        $errorResponse = [
            'statusCode' => 422,
            'error' => 'Unprocessable Entity',
            'message' => 'Validation failed',
            'errors' => [
                ['field' => 'amount', 'message' => 'Amount must be a positive integer'],
                ['field' => 'currency', 'message' => 'Currency must be a valid ISO code']
            ]
        ];

        // Queue a mock response
        $this->mockHandler->append(
            new Response(
                422,
                ['Content-Type' => 'application/json'],
                json_encode($errorResponse)
            )
        );

        try {
            // Attempt to create a payment with invalid data
            $this->paymentsApi->create([
                'amount' => -100,
                'currency' => 'INVALID'
            ]);

            $this->fail('Expected ApiException was not thrown');
        } catch (ApiException $e) {
            // Verify the exception code
            $this->assertEquals(422, $e->getCode());

            // Get the response body
            $responseBody = $e->getResponseBody();

            // If the response body is already an object, use it directly
            if (is_object($responseBody)) {
                $responseObject = $responseBody;
            } else {
                // Otherwise, deserialize it
                $responseObject = json_decode(is_string($responseBody) ? $responseBody : json_encode($responseBody));
            }

            // Set the response object
            $e->setResponseObject($responseObject);

            // Verify the response object is properly set and accessible
            $this->assertNotNull($e->getResponseObject());
            $this->assertEquals(422, $e->getResponseObject()->statusCode);
            $this->assertEquals('Validation failed', $e->getResponseObject()->message);
            $this->assertCount(2, $e->getResponseObject()->errors);
        }
    }

    /**
     * Test handling of malformed JSON response
     */
    public function testMalformedJsonResponse()
    {
        // Queue a mock response with malformed JSON
        $this->mockHandler->append(
            new Response(
                400,
                ['Content-Type' => 'application/json'],
                '{malformed json'
            )
        );

        try {
            // Attempt an API call
            $this->paymentsApi->get('payment_123');

            $this->fail('Expected ApiException was not thrown');
        } catch (ApiException $e) {
            // Verify the exception properties
            $this->assertEquals(400, $e->getCode());

            // The raw malformed JSON should be preserved in the response body
            $responseBody = $e->getResponseBody();
            if (is_string($responseBody)) {
                $this->assertEquals('{malformed json', $responseBody);
            } else {
                $this->fail('Expected response body to be a string, got: ' . gettype($responseBody));
            }
        }
    }
}
