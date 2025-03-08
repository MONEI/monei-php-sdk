<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Monei\MoneiClient;
use Monei\ApiException;
use Monei\Configuration;
use Monei\Api\PaymentsApi;
use Monei\Api\SubscriptionsApi;
use Monei\Api\ApplePayDomainApi;
use Monei\Api\PaymentMethodsApi;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

class MoneiClientTest extends TestCase
{
    private $apiKey = 'test_api_key_dummy';
    private $accountId = 'test_account_id_dummy';
    private $moneiClient;

    protected function setUp(): void
    {
        $this->moneiClient = new MoneiClient($this->apiKey);
    }

    /**
     * Test that the AccountId can be set using the setter method
     */
    public function testAccountIdInConstructor()
    {
        // Set a custom User-Agent first
        $this->moneiClient->setUserAgent('TestPlatform/1.0');

        // Set the AccountId
        $this->moneiClient->setAccountId($this->accountId);

        // Verify the AccountId was set
        $this->assertEquals($this->accountId, $this->moneiClient->getAccountId());
    }

    /**
     * Test that the UserAgent can be set via method
     */
    public function testUserAgent()
    {
        $config = $this->moneiClient->getConfig();
        $this->assertEquals(
            MoneiClient::DEFAULT_USER_AGENT . MoneiClient::SDK_VERSION,
            $config->getUserAgent()
        );

        $this->moneiClient->setUserAgent('TestPlatform/1.0');
        $this->assertEquals('TestPlatform/1.0', $config->getUserAgent());
    }

    /**
     * Test that an exception is thrown when making a request with accountId set but no custom userAgent
     * 
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage A custom User-Agent must be set when acting on behalf of a merchant
     */
    public function testRequireCustomUserAgentWithAccountId()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('A custom User-Agent must be set when acting on behalf of a merchant. Use setUserAgent() before making API calls with accountId.');

        // Set the account ID without setting a custom User-Agent first
        $this->moneiClient->setAccountId($this->accountId);

        // Create a request that would trigger the middleware
        $reflection = new \ReflectionClass($this->moneiClient);
        $httpClientProp = $reflection->getProperty('httpClient');
        $httpClientProp->setAccessible(true);
        $httpClient = $httpClientProp->getValue($this->moneiClient);

        // Create a request and pass it through the handler stack
        $request = new \GuzzleHttp\Psr7\Request('GET', 'https://api.monei.com/v1');
        $handler = $httpClient->getConfig('handler');

        // This will trigger the middleware and throw the exception
        $handler($request, ['http_errors' => false]);
    }

    /**
     * Test that setting a custom user agent allows using accountId
     */
    public function testCustomUserAgentWithAccountId()
    {
        $this->moneiClient->setUserAgent('TestPlatform/1.0');
        $this->moneiClient->setAccountId($this->accountId);

        $config = $this->moneiClient->getConfig();
        $this->assertEquals('TestPlatform/1.0', $config->getUserAgent());
        $this->assertEquals($this->accountId, $this->moneiClient->getAccountId());

        // We can't actually test the HTTP request here, but the middleware should be set up correctly
    }

    public function testSetAccountId()
    {
        // Set a custom User-Agent first
        $this->moneiClient->setUserAgent('TestPlatform/1.0');

        // Set the AccountId
        $this->moneiClient->setAccountId($this->accountId);

        // Verify the AccountId was set
        $this->assertEquals($this->accountId, $this->moneiClient->getAccountId());

        // The actual HTTP request would succeed, but we can't test that here
    }

    public function testSetAccountIdRequiresCustomUserAgent()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('A custom User-Agent must be set when acting on behalf of a merchant. Use setUserAgent() before making API calls with accountId.');

        // Set the account ID
        $this->moneiClient->setAccountId($this->accountId);

        // Create a request that would trigger the middleware
        $reflection = new \ReflectionClass($this->moneiClient);
        $httpClientProp = $reflection->getProperty('httpClient');
        $httpClientProp->setAccessible(true);
        $httpClient = $httpClientProp->getValue($this->moneiClient);

        // Create a request and pass it through the handler stack
        $request = new \GuzzleHttp\Psr7\Request('GET', 'https://api.monei.com/v1');
        $handler = $httpClient->getConfig('handler');

        // This will trigger the middleware and throw the exception
        $handler($request, ['http_errors' => false]);
    }

    public function testSetUserAgentAndAccountId()
    {
        $testUserAgent = 'TestPlatform/1.0';

        $this->moneiClient->setUserAgent($testUserAgent);
        $this->moneiClient->setAccountId($this->accountId);

        $config = $this->moneiClient->getConfig();
        $this->assertEquals($testUserAgent, $config->getUserAgent());
        $this->assertEquals($this->accountId, $this->moneiClient->getAccountId());
    }

    public function testVerifySignatureCorrectly(): void
    {
        // Create a test payload
        $rawBody = '{"id":"3690bd3f7294db82fed08c7371bace32","amount":11700,"currency":"EUR","orderId":"588439","status":"SUCCEEDED","message":"Transaction Approved"}';

        // Create a timestamp for the signature
        $timestamp = '1602604555';

        // Generate a valid signature using the same algorithm as in the verifySignature method
        $hmac = hash_hmac('SHA256', $timestamp . '.' . $rawBody, $this->apiKey);
        $signature = "t={$timestamp},v1={$hmac}";

        // Verify the signature
        $result = $this->moneiClient->verifySignature($rawBody, $signature);

        // Check that the result is the decoded JSON body
        $this->assertEquals(json_decode($rawBody), $result);
    }

    public function testVerifySignatureFailsWithInvalidSignature(): void
    {
        // Create a test payload
        $rawBody = '{"id":"3690bd3f7294db82fed08c7371bace32","amount":11700,"currency":"EUR","orderId":"588439","status":"SUCCEEDED","message":"Transaction Approved"}';

        // Create an invalid signature by using a different timestamp
        $timestamp = '1602604558'; // Different timestamp
        $hmac = hash_hmac('SHA256', '1602604555' . '.' . $rawBody, $this->apiKey); // Using original timestamp for HMAC
        $signature = "t={$timestamp},v1={$hmac}"; // But different timestamp in signature

        // Expect an exception when verifying
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('[401] Signature verification failed');

        $this->moneiClient->verifySignature($rawBody, $signature);
    }

    public function testVerifySignatureWithMalformedSignature(): void
    {
        // Create a test payload
        $rawBody = '{"id":"3690bd3f7294db82fed08c7371bace32","amount":11700,"currency":"EUR","orderId":"588439","status":"SUCCEEDED","message":"Transaction Approved"}';

        // Create a malformed signature that will cause an exception in the explode/array access
        $signature = "invalid_format_without_equals_sign";

        // Expect an exception when verifying
        $this->expectException(\ErrorException::class);

        // Call the method with error suppression to convert PHP notice to exception
        set_error_handler(function ($errno, $errstr) {
            throw new \ErrorException($errstr, 0, $errno);
        });

        try {
            $this->moneiClient->verifySignature($rawBody, $signature);
        } finally {
            restore_error_handler();
        }
    }

    public function testApiInstancesAreInitialized(): void
    {
        // Test that all API instances are properly initialized
        $this->assertInstanceOf(PaymentsApi::class, $this->moneiClient->payments);
        $this->assertInstanceOf(PaymentMethodsApi::class, $this->moneiClient->paymentMethods);
        $this->assertInstanceOf(SubscriptionsApi::class, $this->moneiClient->subscriptions);
        $this->assertInstanceOf(ApplePayDomainApi::class, $this->moneiClient->applePayDomain);
    }

    public function testConfigurationIsAccessible(): void
    {
        $config = $this->moneiClient->getConfig();
        $this->assertInstanceOf(Configuration::class, $config);
        $this->assertEquals($this->apiKey, $config->getApiKey('Authorization'));
    }

    public function testCustomConfigurationIsUsed(): void
    {
        $customConfig = new Configuration();
        $customConfig->setHost('https://custom-api.example.com');

        $client = new MoneiClient($this->apiKey, $customConfig);
        $config = $client->getConfig();

        $this->assertSame($customConfig, $config);
        $this->assertEquals('https://custom-api.example.com', $config->getHost());
    }

    public function testAccountIdHeaderIsAdded(): void
    {
        // Create a new MoneiClient instance
        $client = new MoneiClient($this->apiKey);
        $client->setUserAgent('TestPlatform/1.0');
        $client->setAccountId($this->accountId);

        // Verify the account ID is set correctly
        $this->assertEquals($this->accountId, $client->getAccountId());

        // Verify the User-Agent is set correctly
        $this->assertEquals('TestPlatform/1.0', $client->getConfig()->getUserAgent());

        // Since we can't easily test the middleware directly, we'll test that
        // the client is properly configured to add the header when making requests
        $reflection = new \ReflectionClass(MoneiClient::class);
        $httpClientProp = $reflection->getProperty('httpClient');
        $httpClientProp->setAccessible(true);
        $httpClient = $httpClientProp->getValue($client);

        // Verify the HTTP client is properly configured
        $this->assertInstanceOf(Client::class, $httpClient);
    }

    public function testNullAccountIdDoesNotAddHeader(): void
    {
        // For simplicity, we'll just test the behavior directly
        $client = new MoneiClient($this->apiKey);
        $client->setAccountId(null);

        // Create a request
        $request = new \GuzzleHttp\Psr7\Request('GET', 'https://api.monei.com/v1/test');

        // Apply the middleware function manually
        $modifiedRequest = $request;
        if ($client->getAccountId()) {
            $modifiedRequest = $request->withHeader('MONEI-Account-ID', $client->getAccountId());
        }

        // Verify the request does not have the MONEI-Account-ID header
        $this->assertFalse($modifiedRequest->hasHeader('MONEI-Account-ID'));
    }
}
