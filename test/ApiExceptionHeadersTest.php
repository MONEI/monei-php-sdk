<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Monei\ApiException;

class ApiExceptionHeadersTest extends TestCase
{
    /**
     * Test that headers are properly stored and accessible
     */
    public function testHeadersStorage()
    {
        // Create headers array
        $headers = [
            'Content-Type' => 'application/json',
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Remaining' => '0',
            'X-RateLimit-Reset' => '1614556800'
        ];

        // Create an ApiException with these headers
        $exception = new ApiException('Test error', 400, $headers, '{"error": "Bad Request"}');

        // Get the headers from the exception
        $returnedHeaders = $exception->getResponseHeaders();

        // Verify the headers are stored correctly
        $this->assertSame($headers, $returnedHeaders);

        // Verify individual headers can be accessed
        $this->assertArrayHasKey('Content-Type', $returnedHeaders);
        $this->assertArrayHasKey('X-RateLimit-Limit', $returnedHeaders);
        $this->assertArrayHasKey('X-RateLimit-Remaining', $returnedHeaders);
        $this->assertArrayHasKey('X-RateLimit-Reset', $returnedHeaders);

        // Verify header values
        $this->assertEquals('application/json', $returnedHeaders['Content-Type']);
        $this->assertEquals('100', $returnedHeaders['X-RateLimit-Limit']);
        $this->assertEquals('0', $returnedHeaders['X-RateLimit-Remaining']);
        $this->assertEquals('1614556800', $returnedHeaders['X-RateLimit-Reset']);
    }

    /**
     * Test that empty headers are handled correctly
     */
    public function testEmptyHeaders()
    {
        // Create an ApiException with empty headers
        $exception = new ApiException('Test error', 400, [], '{"error": "Bad Request"}');

        // Get the headers from the exception
        $returnedHeaders = $exception->getResponseHeaders();

        // Verify the headers are an empty array
        $this->assertIsArray($returnedHeaders);
        $this->assertEmpty($returnedHeaders);
    }

    /**
     * Test that null headers are handled correctly
     */
    public function testNullHeaders()
    {
        // Create an ApiException with null headers
        $exception = new ApiException('Test error', 400, null, '{"error": "Bad Request"}');

        // Get the headers from the exception
        $returnedHeaders = $exception->getResponseHeaders();

        // Verify the headers are an empty array (or null, depending on implementation)
        $this->assertTrue(is_array($returnedHeaders) || is_null($returnedHeaders));

        if (is_array($returnedHeaders)) {
            $this->assertEmpty($returnedHeaders);
        }
    }

    /**
     * Test that response body is properly stored and accessible
     */
    public function testResponseBodyStorage()
    {
        // Create a JSON response body
        $responseBody = '{"error": "Bad Request", "message": "Invalid parameter"}';

        // Create an ApiException with this response body
        $exception = new ApiException('Test error', 400, [], $responseBody);

        // Get the response body from the exception
        $returnedBody = $exception->getResponseBody();

        // Verify the response body is stored correctly
        $this->assertSame($responseBody, $returnedBody);
    }

    /**
     * Test that response object is properly stored and accessible
     */
    public function testResponseObjectStorage()
    {
        // Create a response object
        $responseObject = (object)['error' => 'Bad Request', 'message' => 'Invalid parameter'];

        // Create an ApiException
        $exception = new ApiException('Test error', 400, [], json_encode($responseObject));

        // Set the response object
        $exception->setResponseObject($responseObject);

        // Get the response object from the exception
        $returnedObject = $exception->getResponseObject();

        // Verify the response object is stored correctly
        $this->assertSame($responseObject, $returnedObject);
        $this->assertEquals('Bad Request', $returnedObject->error);
        $this->assertEquals('Invalid parameter', $returnedObject->message);
    }

    /**
     * Test with a real-world error response
     */
    public function testRealWorldErrorResponse()
    {
        // Create a real-world error response
        $responseBody = json_encode([
            'statusCode' => 429,
            'error' => 'Too Many Requests',
            'message' => 'Rate limit exceeded'
        ]);

        $headers = [
            'Content-Type' => 'application/json',
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Remaining' => '0',
            'X-RateLimit-Reset' => '1614556800'
        ];

        // Create an ApiException with this response
        $exception = new ApiException('Rate limit exceeded', 429, $headers, $responseBody);

        // Verify the exception properties
        $this->assertEquals(429, $exception->getCode());
        $this->assertEquals('Rate limit exceeded', $exception->getMessage());

        // Verify the headers
        $returnedHeaders = $exception->getResponseHeaders();
        $this->assertEquals('100', $returnedHeaders['X-RateLimit-Limit']);
        $this->assertEquals('0', $returnedHeaders['X-RateLimit-Remaining']);

        // Verify the response body
        $returnedBody = $exception->getResponseBody();
        $this->assertSame($responseBody, $returnedBody);

        // Deserialize and set the response object
        $responseObject = json_decode($responseBody);
        $exception->setResponseObject($responseObject);

        // Verify the response object
        $returnedObject = $exception->getResponseObject();
        $this->assertEquals(429, $returnedObject->statusCode);
        $this->assertEquals('Too Many Requests', $returnedObject->error);
        $this->assertEquals('Rate limit exceeded', $returnedObject->message);
    }
}
