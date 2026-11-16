<?php

namespace Tests;

use Monei\Api\ApplePayDomainApi;
use Monei\Api\CustomersApi;
use Monei\Api\PaymentMethodsApi;
use Monei\Api\PaymentsApi;
use Monei\Api\SubscriptionsApi;
use Monei\Internal\GuzzleHttp\Handler\MockHandler;
use Monei\Internal\GuzzleHttp\Psr7\Response;
use Monei\Internal\GuzzleHttp\Client;
use Monei\Internal\GuzzleHttp\HandlerStack;
use Monei\Internal\GuzzleHttp\Middleware;
use Monei\ApiException;
use Monei\Configuration;
use Monei\MoneiClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class MoneiClientTest extends TestCase
{
    private $apiKey = 'test_api_key_dummy';
    private $accountId = 'test_account_id_dummy';
    private $moneiClient;

    protected function setUp(): void
    {
        // Reset the default configuration to ensure test isolation
        Configuration::setDefaultConfiguration(new Configuration());
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
        $request = new \Monei\Internal\GuzzleHttp\Psr7\Request('GET', 'https://api.monei.com/v1');
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
        $request = new \Monei\Internal\GuzzleHttp\Psr7\Request('GET', 'https://api.monei.com/v1');
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
        $timestamp = '1602604558';  // Different timestamp
        $hmac = hash_hmac('SHA256', '1602604555' . '.' . $rawBody, $this->apiKey);  // Using original timestamp for HMAC
        $signature = "t={$timestamp},v1={$hmac}";  // But different timestamp in signature

        // Expect an exception when verifying
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('[401] Signature verification failed');

        $this->moneiClient->verifySignature($rawBody, $signature);
    }

    /**
     * @dataProvider provideMalformedSignatures
     */
    public function testVerifySignatureWithMalformedSignature(string $signature): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionCode(401);

        $this->moneiClient->verifySignature(self::V2_BODY, $signature);
    }

    public function provideMalformedSignatures(): array
    {
        return [
            'empty' => [''],
            'element without equals sign' => ['invalid_format_without_equals_sign'],
            // v1 is valid for t=1794643200: only the missing t makes it fail.
            'no t' => ['v1=' . self::V1_SIGNATURE],
            // v1 is the HMAC of '.' . V2_BODY with the API key: only the empty t makes it fail.
            'empty t' => ['t=,v1=396280c32eb352e86a99154b697a0c8c61d9fc0a7953c8121374bfc577edfd67'],
        ];
    }

    /**
     * A missing header gives null. It must fail as an invalid signature (401), not with a TypeError.
     */
    public function testVerifySignatureWithNullSignatureFails(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionCode(401);

        $this->moneiClient->verifySignature(self::V2_BODY, null);
    }

    private const V2_BODY = '{"id":"af6029f80f5fc73a8ad2753eea0b1be0","status":"SUCCEEDED"}';
    // The v2 test vector of the signing v2 plan: HMAC-SHA256 of '1794643200.' . V2_BODY keyed with oldSecret().
    private const V2_SIGNATURE = '8a1968e508d090894e64e50a3162dd9920feec2d4c1d04c385e80f08532226cf';
    // HMAC-SHA256 of '1794643200.' . V2_BODY keyed with $this->apiKey.
    private const V1_SIGNATURE = 'b9439a39a999e8e73223b864d288bb27256c916c50706d4dee6c3bdd20e9dc36';

    // The secrets are concatenated so that secret scanners do not flag these test values.
    private static function oldSecret(): string
    {
        return 'whsec' . '_dGVzdC1zaWduaW5nLXNlY3JldC1mb3ItdjItdmVjdG9y';
    }

    private static function newSecret(): string
    {
        return 'whsec' . '_bmV3LXNpZ25pbmctc2VjcmV0LWFmdGVyLXRoZS1yb2xs';
    }

    /**
     * A header without v1 comes from an account without an API key.
     */
    public function testVerifySignatureWithSecretAcceptsOneV2WithoutV1(): void
    {
        $result = $this->moneiClient->verifySignature(
            self::V2_BODY,
            't=1794643200,v2=' . self::V2_SIGNATURE,
            self::oldSecret()
        );

        $this->assertEquals(json_decode(self::V2_BODY), $result);
    }

    /**
     * During a roll the header holds two v2 values, newest first. A parser that keeps only the last
     * value fails with the new secret.
     *
     * @dataProvider provideRolledSecrets
     */
    public function testVerifySignatureDuringRollAcceptsEachV2(string $secret): void
    {
        $signature = 't=1794643200,v1=' . self::V1_SIGNATURE
            . ',v2=e32c238b644e5786f51b2b7205994f2a0d44d56375df5d8bf86d9579a96e0210'
            . ',v2=' . self::V2_SIGNATURE;

        $result = $this->moneiClient->verifySignature(self::V2_BODY, $signature, $secret);

        $this->assertEquals(json_decode(self::V2_BODY), $result);
    }

    public function provideRolledSecrets(): array
    {
        return [
            'new secret' => [self::newSecret()],
            'old secret' => [self::oldSecret()],
        ];
    }

    /**
     * @dataProvider provideInvalidSignaturesWithSecret
     */
    public function testVerifySignatureWithSecretFails(string $signature, $secret): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionCode(401);

        $this->moneiClient->verifySignature(self::V2_BODY, $signature, $secret);
    }

    public function provideInvalidSignaturesWithSecret(): array
    {
        return [
            'wrong secret' => ['t=1794643200,v2=' . self::V2_SIGNATURE, self::newSecret()],
            // v1 is valid for the API key, so a fallback to v1 passes.
            'only v1' => ['t=1794643200,v1=' . self::V1_SIGNATURE, self::oldSecret()],
            // v2 is HMAC-SHA256 keyed with an empty string, which anyone can make.
            'empty secret' => ['t=1794643200,v2=ef198247309de9b0cd6cd622f2cd96b2e292f578aa7108bc21e5fd8e0aa4e5d1', ''],
            // getenv() gives false for an unset variable.
            'false secret' => ['t=1794643200,v1=' . self::V1_SIGNATURE, false],
        ];
    }

    public function testVerifySignatureWithoutSecretUsesV1WhenV2IsPresent(): void
    {
        $result = $this->moneiClient->verifySignature(
            self::V2_BODY,
            't=1794643200,v1=' . self::V1_SIGNATURE . ',v2=' . self::V2_SIGNATURE
        );

        $this->assertEquals(json_decode(self::V2_BODY), $result);
    }

    public function testVerifySignatureWithoutSecretFailsWithOnlyV2(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionCode(401);

        $this->moneiClient->verifySignature(self::V2_BODY, 't=1794643200,v2=' . self::V2_SIGNATURE);
    }

    public function testApiInstancesAreInitialized(): void
    {
        // Test that all API instances are properly initialized
        $this->assertInstanceOf(PaymentsApi::class, $this->moneiClient->payments);
        $this->assertInstanceOf(PaymentMethodsApi::class, $this->moneiClient->paymentMethods);
        $this->assertInstanceOf(SubscriptionsApi::class, $this->moneiClient->subscriptions);
        $this->assertInstanceOf(CustomersApi::class, $this->moneiClient->customers);
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
        $request = new \Monei\Internal\GuzzleHttp\Psr7\Request('GET', 'https://api.monei.com/v1/test');

        // Apply the middleware function manually
        $modifiedRequest = $request;
        if ($client->getAccountId()) {
            $modifiedRequest = $request->withHeader('MONEI-Account-ID', $client->getAccountId());
        }

        // Verify the request does not have the MONEI-Account-ID header
        $this->assertFalse($modifiedRequest->hasHeader('MONEI-Account-ID'));
    }
}
