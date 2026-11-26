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

use Monei\Api\ApplePayCertificateApi;
use Monei\Api\ApplePayDomainApi;
use Monei\Api\BizumApi;
use Monei\Api\CheckoutSessionsApi;
use Monei\Api\CustomersApi;
use Monei\Api\PaymentMethodsApi;
use Monei\Api\PaymentsApi;
use Monei\Api\POSAuthTokenApi;
use Monei\Api\PricesApi;
use Monei\Api\ProductsApi;
use Monei\Api\SubscriptionsApi;
use Monei\Api\TaxRatesApi;
use Monei\Internal\GuzzleHttp\Client;
use Monei\Internal\GuzzleHttp\HandlerStack;
use Monei\Internal\GuzzleHttp\Middleware;
use Monei\Internal\Psr\Http\Message\RequestInterface;
use Monei\ApiException;
use Monei\Configuration;

/**
 * PaymentsApi Class Doc Comment
 *
 * @category Class
 * @package  Monei\
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class MoneiClient
{
    /** @var string */
    public const SDK_VERSION = '2.18.0';
    /** @var string */
    public const DEFAULT_USER_AGENT = 'MONEI/PHP/';
    /** @var Configuration */
    protected $config;
    /** @var PaymentsApi */
    public $payments;
    /** @var PaymentMethodsApi */
    public $paymentMethods;
    /** @var SubscriptionsApi */
    public $subscriptions;
    /** @var CustomersApi */
    public $customers;
    /** @var ProductsApi */
    public $products;
    /** @var PricesApi */
    public $prices;
    /** @var TaxRatesApi */
    public $taxRates;
    /** @var CheckoutSessionsApi */
    public $checkoutSessions;
    /** @var ApplePayDomainApi */
    public $applePayDomain;
    /** @var BizumApi */
    public $bizum;
    /** @var ApplePayCertificateApi */
    public $applePayCertificate;
    /** @var POSAuthTokenApi */
    public $posAuthToken;
    /** @var string|null */
    protected $accountId;
    /** @var Client */
    protected $httpClient;
    /**
     * @param string          $apiKey
     * @param Configuration|null   $config
     */
    public function __construct(string $apiKey, ?Configuration $config = null)
    {
        $this->config = $config ?: Configuration::getDefaultConfiguration();
        $this->config->setApiKey('Authorization', $apiKey);
        // Only set default user agent if no custom user agent was provided
        $currentUserAgent = $this->config->getUserAgent();
        if (empty($currentUserAgent) || strpos($currentUserAgent, 'OpenAPI-Generator') === 0) {
            $this->config->setUserAgent(self::DEFAULT_USER_AGENT . self::SDK_VERSION);
        }
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
        $this->customers = new CustomersApi($this->httpClient, $this->config);
        $this->products = new ProductsApi($this->httpClient, $this->config);
        $this->prices = new PricesApi($this->httpClient, $this->config);
        $this->taxRates = new TaxRatesApi($this->httpClient, $this->config);
        $this->checkoutSessions = new CheckoutSessionsApi($this->httpClient, $this->config);
        $this->applePayDomain = new ApplePayDomainApi($this->httpClient, $this->config);
        $this->applePayCertificate = new ApplePayCertificateApi($this->httpClient, $this->config);
        $this->bizum = new BizumApi($this->httpClient, $this->config);
        $this->posAuthToken = new POSAuthTokenApi($this->httpClient, $this->config);
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
     * Verify the MONEI-Signature header of a callback or a webhook.
     *
     * Without a secret, v1 must match the API key.
     * With a secret (whsec_ or mksec_), one v2 must match the secret. v1 is not used then.
     *
     * @param string      $body      Raw request body
     * @param string      $signature MONEI-Signature header
     * @param string|null $secret    Signing secret for v2
     * @throws ApiException When the signature is not valid
     * @return object
     */
    public function verifySignature($body, $signature, $secret = null)
    {
        $failure = new ApiException('[401] Signature verification failed', 401);
        // An empty key gives an HMAC that anyone can make, for example from an unset getenv().
        if ($secret !== null && (!is_string($secret) || $secret === '')) {
            throw $failure;
        }
        if (!is_string($signature)) {
            throw $failure;
        }
        $parts = ['v2' => []];
        foreach (explode(',', $signature) as $element) {
            $pair = explode('=', $element, 2);
            if (count($pair) !== 2) {
                throw $failure;
            }
            if ($pair[0] === 'v2') {
                $parts['v2'][] = $pair[1];
            } else {
                $parts[$pair[0]] = $pair[1];
            }
        }
        if (!isset($parts['t']) || $parts['t'] === '') {
            throw $failure;
        }
        $payload = $parts['t'] . '.' . $body;
        if ($secret === null) {
            $hmac = hash_hmac('SHA256', $payload, $this->config->getApiKey('Authorization'));
            $signatures = isset($parts['v1']) ? [$parts['v1']] : [];
        } else {
            $hmac = hash_hmac('SHA256', $payload, $secret);
            $signatures = $parts['v2'];
        }
        foreach ($signatures as $candidate) {
            if (hash_equals($hmac, $candidate)) {
                return json_decode($body);
            }
        }
        throw $failure;
    }
}
