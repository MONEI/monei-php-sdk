<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Monei\ApiException;

class ApiExceptionTest extends TestCase
{
    /**
     * Test creating an ApiException with basic parameters
     */
    public function testConstructor()
    {
        $message = 'Test error message';
        $code = 400;
        $responseHeaders = ['Content-Type' => 'application/json'];
        $responseBody = '{"error": "Bad Request", "message": "Invalid parameter"}';

        $exception = new ApiException($message, $code, $responseHeaders, $responseBody);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
        $this->assertEquals($responseHeaders, $exception->getResponseHeaders());
        $this->assertEquals($responseBody, $exception->getResponseBody());
    }

    /**
     * Test creating an ApiException with empty parameters
     */
    public function testConstructorWithEmptyParams()
    {
        $exception = new ApiException();

        $this->assertEquals('', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertEquals([], $exception->getResponseHeaders());
        $this->assertNull($exception->getResponseBody());
    }

    /**
     * Test setting and getting response object
     */
    public function testResponseObject()
    {
        $exception = new ApiException();
        $responseObject = (object)['status' => 'error', 'code' => 'invalid_request'];

        $exception->setResponseObject($responseObject);
        $this->assertEquals($responseObject, $exception->getResponseObject());
    }

    /**
     * Test with JSON response body
     */
    public function testWithJsonResponseBody()
    {
        $jsonBody = '{"error": "Not Found", "message": "Resource not found", "code": "resource_not_found"}';
        $exception = new ApiException('Not Found', 404, ['Content-Type' => 'application/json'], $jsonBody);

        $this->assertEquals($jsonBody, $exception->getResponseBody());
    }

    /**
     * Test with stdClass response body
     */
    public function testWithStdClassResponseBody()
    {
        $stdClassBody = (object)['error' => 'Unauthorized', 'message' => 'Invalid API key'];
        $exception = new ApiException('Unauthorized', 401, ['Content-Type' => 'application/json'], $stdClassBody);

        $this->assertEquals($stdClassBody, $exception->getResponseBody());
    }

    /**
     * Test with various HTTP status codes
     * 
     * @dataProvider provideHttpStatusCodes
     */
    public function testWithVariousHttpStatusCodes($code, $message)
    {
        $exception = new ApiException($message, $code);

        $this->assertEquals($code, $exception->getCode());
        $this->assertEquals($message, $exception->getMessage());
    }

    /**
     * Data provider for HTTP status codes
     */
    public static function provideHttpStatusCodes()
    {
        return [
            [400, 'Bad Request'],
            [401, 'Unauthorized'],
            [403, 'Forbidden'],
            [404, 'Not Found'],
            [422, 'Unprocessable Entity'],
            [429, 'Too Many Requests'],
            [500, 'Internal Server Error'],
            [503, 'Service Unavailable']
        ];
    }

    /**
     * Test exception with complex nested response object
     */
    public function testWithComplexResponseObject()
    {
        $responseBody = json_encode([
            'error' => [
                'type' => 'invalid_request_error',
                'message' => 'The request was unacceptable',
                'param' => 'amount',
                'code' => 'parameter_invalid',
                'doc_url' => 'https://docs.monei.com/api/errors#parameter_invalid'
            ]
        ]);

        $exception = new ApiException('Invalid Request', 400, ['Content-Type' => 'application/json'], $responseBody);

        // Set a deserialized response object
        $decodedObject = json_decode($responseBody);
        $exception->setResponseObject($decodedObject);

        $this->assertEquals($decodedObject, $exception->getResponseObject());
        $this->assertEquals($responseBody, $exception->getResponseBody());
    }

    /**
     * Test exception with rate limit headers
     */
    public function testWithRateLimitHeaders()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Remaining' => '0',
            'X-RateLimit-Reset' => '1614556800'
        ];

        $exception = new ApiException('Too Many Requests', 429, $headers, '{"error": "rate_limit_exceeded"}');

        $this->assertEquals($headers, $exception->getResponseHeaders());
        $this->assertEquals('0', $exception->getResponseHeaders()['X-RateLimit-Remaining']);
    }

    public function testConstructorWithMessageAndCode()
    {
        $exception = new ApiException('Test message', 400);
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
    }

    public function testConstructorWithResponseHeaders()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'X-Request-ID' => '123456'
        ];
        $exception = new ApiException('Test message', 400, $headers);
        $this->assertEquals($headers, $exception->getResponseHeaders());
    }

    public function testConstructorWithResponseBody()
    {
        $body = '{"error": "Bad Request", "message": "Invalid parameter"}';
        $exception = new ApiException('Test message', 400, [], $body);
        $this->assertEquals($body, $exception->getResponseBody());
    }

    public function testSetAndGetResponseObject()
    {
        $obj = (object) ['error' => 'Bad Request', 'message' => 'Invalid parameter'];
        $exception = new ApiException('Test message', 400);
        $exception->setResponseObject($obj);
        $this->assertEquals($obj, $exception->getResponseObject());
    }

    public function testGetResponseObjectWithJsonBody()
    {
        $body = '{"error": "Bad Request", "message": "Invalid parameter"}';
        $exception = new ApiException('Test message', 400, [], $body);

        // Set the response object directly since the getResponseObject method doesn't parse JSON
        $obj = json_decode($body);
        $exception->setResponseObject($obj);

        $responseObj = $exception->getResponseObject();
        $this->assertIsObject($responseObj);
        $this->assertEquals('Bad Request', $responseObj->error);
        $this->assertEquals('Invalid parameter', $responseObj->message);
    }

    public function testGetResponseObjectWithNonJsonBody()
    {
        $body = 'Plain text error message';
        $exception = new ApiException('Test message', 400, [], $body);
        $obj = $exception->getResponseObject();
        $this->assertNull($obj);
    }

    public function testGetResponseObjectWithEmptyBody()
    {
        $exception = new ApiException('Test message', 400, [], '');
        $obj = $exception->getResponseObject();
        $this->assertNull($obj);
    }

    public function testGetResponseObjectWithNullBody()
    {
        $exception = new ApiException('Test message', 400, [], null);
        $obj = $exception->getResponseObject();
        $this->assertNull($obj);
    }

    public function testGetResponseObjectWithInvalidJsonBody()
    {
        $body = '{invalid json}';
        $exception = new ApiException('Test message', 400, [], $body);
        $obj = $exception->getResponseObject();
        $this->assertNull($obj);
    }

    public function testGetResponseHeadersWithNoHeaders()
    {
        $exception = new ApiException('Test message', 400);
        $this->assertEquals([], $exception->getResponseHeaders());
    }

    public function testGetResponseBodyWithNoBody()
    {
        $exception = new ApiException('Test message', 400);
        $this->assertNull($exception->getResponseBody());
    }
}
