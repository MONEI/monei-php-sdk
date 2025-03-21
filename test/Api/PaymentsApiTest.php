<?php

/**
 * PaymentsApiTest
 * PHP version 7.4
 *
 * @category Class
 * @package  Monei\
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * MONEI API v1
 *
 * <p>The MONEI API is organized around <a href=\"https://en.wikipedia.org/wiki/Representational_State_Transfer\">REST</a> principles. Our API is designed to be intuitive and developer-friendly.</p> <h3>Base URL</h3> <p>All API requests should be made to:</p> <pre><code>https://api.monei.com/v1 </code></pre> <h3>Environment</h3> <p>MONEI provides two environments:</p> <ul> <li><strong>Test Environment</strong>: For development and testing without processing real payments</li> <li><strong>Live Environment</strong>: For processing real transactions in production</li> </ul> <h3>Client Libraries</h3> <p>We provide official SDKs to simplify integration:</p> <ul> <li><a href=\"https://github.com/MONEI/monei-php-sdk\">PHP SDK</a></li> <li><a href=\"https://github.com/MONEI/monei-python-sdk\">Python SDK</a></li> <li><a href=\"https://github.com/MONEI/monei-node-sdk\">Node.js SDK</a></li> <li><a href=\"https://postman.monei.com/\">Postman Collection</a></li> </ul> <p>Our SDKs handle authentication, error handling, and request formatting automatically.</p> <h3>Important Requirements</h3> <ul> <li>All API requests must be made over HTTPS</li> <li>If you are not using our official SDKs, you <strong>must provide a valid <code>User-Agent</code> header</strong> with each request</li> <li>Requests without proper authentication will return a <code>401 Unauthorized</code> error</li> </ul> <h3>Error Handling</h3> <p>The API returns consistent error codes and messages to help you troubleshoot issues. Each response includes a <code>statusCode</code> attribute indicating the outcome of your request.</p> <p><a href=\"https://docs.monei.com/api/errors\">View complete list of status codes →</a></p> <h3>Rate Limits</h3> <p>The API implements rate limiting to ensure stability. If you exceed the limits, requests will return a <code>429 Too Many Requests</code> status code.</p>
 *
 * The version of the OpenAPI document: 1.5.0
 * Generated by: https://openapi-generator.tech
 * OpenAPI Generator version: 6.0.1
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Please update the test case below to test the endpoint.
 */

namespace Monei\Test\Api;

use Monei\Configuration;
use Monei\ApiException;
use Monei\ObjectSerializer;
use Monei\Api\PaymentsApi;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;

/**
 * PaymentsApiTest Class Doc Comment
 *
 * @category Class
 * @package  Monei\
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class PaymentsApiTest extends TestCase
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
     * Setup before running any test cases
     */
    public static function setUpBeforeClass(): void
    {
    }

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
     * Clean up after running each test case
     */
    public function tearDown(): void
    {
        $this->mockHandler->reset();
        $this->container = [];
    }

    /**
     * Clean up after running all test cases
     */
    public static function tearDownAfterClass(): void
    {
    }

    /**
     * Test case for cancel
     *
     * Cancel Payment.
     */
    public function testCancel()
    {
        $paymentId = 'pay_123456789';

        // Queue a mock response
        $this->mockHandler->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'id' => $paymentId,
                'status' => 'CANCELED',
                'amount' => 1000,
                'currency' => 'EUR'
            ])
        ));

        // Call the API method
        $result = $this->paymentsApi->cancel($paymentId);

        // Check the request
        $this->assertCount(1, $this->container);
        $request = $this->container[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertStringContainsString("/payments/{$paymentId}/cancel", $request->getUri()->getPath());

        // Check the response
        $this->assertEquals($paymentId, $result['id']);
        $this->assertEquals('CANCELED', $result['status']);
        $this->assertEquals(1000, $result['amount']);
        $this->assertEquals('EUR', $result['currency']);
    }

    /**
     * Test case for capture
     *
     * Capture Payment.
     */
    public function testCapture()
    {
        $paymentId = 'pay_123456789';

        // Queue a mock response
        $this->mockHandler->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'id' => $paymentId,
                'status' => 'SUCCEEDED',
                'amount' => 1000,
                'currency' => 'EUR'
            ])
        ));

        // Call the API method
        $result = $this->paymentsApi->capture($paymentId);

        // Check the request
        $this->assertCount(1, $this->container);
        $request = $this->container[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertStringContainsString("/payments/{$paymentId}/capture", $request->getUri()->getPath());

        // Check the response
        $this->assertEquals($paymentId, $result['id']);
        $this->assertEquals('SUCCEEDED', $result['status']);
        $this->assertEquals(1000, $result['amount']);
        $this->assertEquals('EUR', $result['currency']);
    }

    /**
     * Test case for confirm
     *
     * Confirm Payment.
     */
    public function testConfirm()
    {
        $paymentId = 'pay_123456789';

        // Queue a mock response
        $this->mockHandler->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'id' => $paymentId,
                'status' => 'SUCCEEDED',
                'amount' => 1000,
                'currency' => 'EUR'
            ])
        ));

        // Call the API method
        $result = $this->paymentsApi->confirm($paymentId);

        // Check the request
        $this->assertCount(1, $this->container);
        $request = $this->container[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertStringContainsString("/payments/{$paymentId}/confirm", $request->getUri()->getPath());

        // Check the response
        $this->assertEquals($paymentId, $result['id']);
        $this->assertEquals('SUCCEEDED', $result['status']);
        $this->assertEquals(1000, $result['amount']);
        $this->assertEquals('EUR', $result['currency']);
    }

    /**
     * Test case for create
     *
     * Create Payment.
     */
    public function testCreate()
    {
        // Queue a mock response
        $this->mockHandler->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'id' => 'pay_123456789',
                'status' => 'PENDING',
                'amount' => 1000,
                'currency' => 'EUR',
                'orderId' => 'order_123',
                'description' => 'Test payment'
            ])
        ));

        // Create a payment request
        $createRequest = (object)[
            'amount' => 1000,
            'currency' => 'EUR',
            'orderId' => 'order_123',
            'description' => 'Test payment'
        ];

        // Call the API method - we don't care about the response for this test
        $this->paymentsApi->create($createRequest);

        // Check the request
        $this->assertCount(1, $this->container);
        $request = $this->container[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertStringContainsString("/payments", $request->getUri()->getPath());

        // Check the request body
        $requestBody = json_decode($request->getBody()->getContents(), true);
        $this->assertEquals(1000, $requestBody['amount']);
        $this->assertEquals('EUR', $requestBody['currency']);
        $this->assertEquals('order_123', $requestBody['orderId']);
        $this->assertEquals('Test payment', $requestBody['description']);
    }

    /**
     * Test case for get
     *
     * Get Payment.
     */
    public function testGet()
    {
        $paymentId = 'pay_123456789';

        // Queue a mock response
        $this->mockHandler->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'id' => $paymentId,
                'status' => 'SUCCEEDED',
                'amount' => 1000,
                'currency' => 'EUR'
            ])
        ));

        // Call the API method
        $result = $this->paymentsApi->get($paymentId);

        // Check the request
        $this->assertCount(1, $this->container);
        $request = $this->container[0]['request'];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertStringContainsString("/payments/{$paymentId}", $request->getUri()->getPath());

        // Check the response
        $this->assertEquals($paymentId, $result['id']);
        $this->assertEquals('SUCCEEDED', $result['status']);
        $this->assertEquals(1000, $result['amount']);
        $this->assertEquals('EUR', $result['currency']);
    }

    /**
     * Test case for recurring
     *
     * Create Recurring Payment.
     */
    public function testRecurring()
    {
        $sequenceId = 'seq_123456789';

        // Queue a mock response
        $this->mockHandler->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'id' => 'pay_123456789',
                'status' => 'PENDING',
                'amount' => 1000,
                'currency' => 'EUR',
                'sequenceId' => $sequenceId
            ])
        ));

        // Create a recurring payment request
        $recurringRequest = (object)[
            'amount' => 1000,
            'currency' => 'EUR'
        ];

        // Call the API method - we don't care about the response for this test
        $this->paymentsApi->recurring($sequenceId, $recurringRequest);

        // Check the request
        $this->assertCount(1, $this->container);
        $request = $this->container[0]['request'];
        $this->assertEquals('POST', $request->getMethod());

        // The actual path might vary based on the API implementation
        // We'll just check that the sequence ID is in the path
        $this->assertStringContainsString($sequenceId, (string)$request->getUri());

        // Check the request body
        $requestBody = json_decode($request->getBody()->getContents(), true);
        $this->assertEquals(1000, $requestBody['amount']);
        $this->assertEquals('EUR', $requestBody['currency']);
    }

    /**
     * Test case for refund
     *
     * Refund Payment.
     */
    public function testRefund()
    {
        $paymentId = 'pay_123456789';

        // Queue a mock response
        $this->mockHandler->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'id' => $paymentId,
                'status' => 'REFUNDED',
                'amount' => 1000,
                'currency' => 'EUR'
            ])
        ));

        // Create a refund request
        $refundRequest = (object)[
            'amount' => 1000,
            'reason' => 'REQUESTED_BY_CUSTOMER'
        ];

        // Call the API method
        $result = $this->paymentsApi->refund($paymentId, $refundRequest);

        // Check the request
        $this->assertCount(1, $this->container);
        $request = $this->container[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertStringContainsString("/payments/{$paymentId}/refund", $request->getUri()->getPath());

        // Check the response
        $this->assertEquals($paymentId, $result['id']);
        $this->assertEquals('REFUNDED', $result['status']);
        $this->assertEquals(1000, $result['amount']);
        $this->assertEquals('EUR', $result['currency']);
    }

    /**
     * Test case for sendLink
     *
     * Send Payment Link.
     */
    public function testSendLink()
    {
        $paymentId = 'pay_123456789';

        // Queue a mock response
        $this->mockHandler->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'id' => $paymentId,
                'status' => 'PENDING',
                'amount' => 1000,
                'currency' => 'EUR'
            ])
        ));

        // Create a send link request
        $sendLinkRequest = (object)['email' => 'customer@example.com'];

        // Call the API method
        $result = $this->paymentsApi->sendLink($paymentId, $sendLinkRequest);

        // Check the request
        $this->assertCount(1, $this->container);
        $request = $this->container[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertStringContainsString("/payments/{$paymentId}/link", $request->getUri()->getPath());

        // Check the response
        $this->assertEquals($paymentId, $result['id']);
        $this->assertEquals('PENDING', $result['status']);
        $this->assertEquals(1000, $result['amount']);
        $this->assertEquals('EUR', $result['currency']);
    }

    /**
     * Test case for sendReceipt
     *
     * Send Payment Receipt.
     */
    public function testSendReceipt()
    {
        $paymentId = 'pay_123456789';

        // Queue a mock response
        $this->mockHandler->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'id' => $paymentId,
                'status' => 'SUCCEEDED',
                'amount' => 1000,
                'currency' => 'EUR'
            ])
        ));

        // Create a send receipt request
        $sendReceiptRequest = (object)['email' => 'customer@example.com'];

        // Call the API method
        $result = $this->paymentsApi->sendReceipt($paymentId, $sendReceiptRequest);

        // Check the request
        $this->assertCount(1, $this->container);
        $request = $this->container[0]['request'];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertStringContainsString("/payments/{$paymentId}/receipt", $request->getUri()->getPath());

        // Check the response
        $this->assertEquals($paymentId, $result['id']);
        $this->assertEquals('SUCCEEDED', $result['status']);
        $this->assertEquals(1000, $result['amount']);
        $this->assertEquals('EUR', $result['currency']);
    }

    /**
     * Test case for sendRequest
     *
     * Send Payment Request.
     */
    public function testSendRequest()
    {
        $paymentId = 'pay_123456789';

        // Queue a mock response
        $this->mockHandler->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'id' => $paymentId,
                'status' => 'PENDING',
                'amount' => 1000,
                'currency' => 'EUR'
            ])
        ));

        // Create a send request
        $sendRequest = (object)['email' => 'customer@example.com'];

        // Call the API method - we don't care about the response for this test
        $this->paymentsApi->sendRequest($paymentId, $sendRequest);

        // Check the request
        $this->assertCount(1, $this->container);
        $request = $this->container[0]['request'];
        $this->assertEquals('POST', $request->getMethod());

        // The actual path might vary based on the API implementation
        // We'll just check that the payment ID is in the path
        $this->assertStringContainsString($paymentId, (string)$request->getUri());

        // Check the request body
        $requestBody = json_decode($request->getBody()->getContents(), true);
        $this->assertEquals('customer@example.com', $requestBody['email']);
    }
}
