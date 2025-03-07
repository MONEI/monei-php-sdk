<?php

/**
 * MONEI Payment Gateway API v1
 * MoneiClient
 * PHP version 5
 *
 * @category Class
 * @package  Monei\MoneiClient
 * @author   MONEI
 * @link     https://monei.com
 */

namespace Monei;

use OpenAPI\Client\ApiException;
use OpenAPI\Client\Configuration;
use OpenAPI\Client\Api\PaymentsApi;
use OpenAPI\Client\Api\SubscriptionsApi;
use OpenAPI\Client\Api\ApplePayDomainApi;
use OpenAPI\Client\Api\PaymentMethodsApi;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

/**
 * PaymentsApi Class Doc Comment
 *
 * @category Class
 * @package  OpenAPI\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class MoneiClient
{
    /**
     * @var string
     */
    public const SDK_VERSION = '2.6.9';

    /**
     * @var string
     */
    public const DEFAULT_USER_AGENT = 'MONEI/PHP/';

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var PaymentsApi
     */
    public $payments;

    /**
     * @var PaymentMethodsApi
     */
    public $paymentMethods;

    /**
     * @var SubscriptionsApi
     */
    public $subscriptions;

    /**
     * @var ApplePayDomainApi
     */
    public $applePayDomain;

    /**
     * @var string|null
     */
    protected $accountId;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @param string          $apiKey
     * @param Configuration   $config
     */
    public function __construct(
        string $apiKey,
        Configuration $config = null
    ) {
        $this->config = $config ?: Configuration::getDefaultConfiguration();
        $this->config->setApiKey('Authorization', $apiKey);
        $this->config->setUserAgent(self::DEFAULT_USER_AGENT . self::SDK_VERSION);

        // Create a custom HTTP client with middleware to add the AccountId header if needed
        $stack = HandlerStack::create();
        $stack->push(Middleware::mapRequest(function (RequestInterface $request) {
            if ($this->accountId) {
                // If accountId is set, require a custom User-Agent
                if ($this->config->getUserAgent() === self::DEFAULT_USER_AGENT . self::SDK_VERSION) {
                    throw new \InvalidArgumentException('A custom User-Agent must be set when acting on behalf of a merchant. Use setUserAgent() before making API calls with accountId.');
                }
                return $request->withHeader('MONEI-Account-ID', $this->accountId);
            }
            return $request;
        }));

        $this->httpClient = new Client(['handler' => $stack]);

        $this->payments = new PaymentsApi($this->httpClient, $this->config);
        $this->paymentMethods = new PaymentMethodsApi($this->httpClient, $this->config);
        $this->subscriptions = new SubscriptionsApi($this->httpClient, $this->config);
        $this->applePayDomain = new ApplePayDomainApi($this->httpClient, $this->config);
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set the account ID to act on behalf of a merchant
     * 
     * @param string|null $accountId The merchant's account ID
     * @return void
     */
    public function setAccountId(?string $accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * Get the current account ID
     * 
     * @return string|null The current account ID
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set a custom User-Agent header
     * 
     * @param string $userAgent Custom User-Agent string
     * @return void
     */
    public function setUserAgent(string $userAgent)
    {
        $this->config->setUserAgent($userAgent);
    }

    /**
     * @param string    $body
     * @param string    $signature
     * @return object
     */
    public function verifySignature($body, $signature)
    {
        $parts = array_reduce(explode(',', $signature), function ($result, $part) {
            [$key, $value] = explode('=', $part);
            $result[$key] = $value;
            return $result;
        }, []);

        $hmac = hash_hmac('SHA256', $parts['t'] . '.' . $body, $this->config->getApiKey('Authorization'));

        if ($hmac !== $parts['v1']) {
            throw new ApiException('[401] Signature verification failed', 401);
        }

        return json_decode($body);
    }
}
