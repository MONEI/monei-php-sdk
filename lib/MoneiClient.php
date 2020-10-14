<?php
/**
 * MONEI Payment Gateway API v1
 * MoneiClient
 * PHP version 5
 *
 * @category Class
 * @package  Monei\MoneiClient
 * @author   MONEI
 * @link     https://monei.net
 */

namespace Monei;

use OpenAPI\Client\Configuration;
use OpenAPI\Client\Api\PaymentsApi;

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
     * @var Configuration
     */
    protected $config;

    /**
     * @var PaymentsApi
     */
    public $payments;

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
        $this->config->setUserAgent('MONEI/PHP/0.1.2');

        $this->payments = new PaymentsApi(null, $this->config);
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }
}
