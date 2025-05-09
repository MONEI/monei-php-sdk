<?php

/**
 * PaymentMethodsMetadataClickToPayTest
 *
 * PHP version 7.4
 *
 * @category Class
 * @package  Monei
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * MONEI API v1
 *
 * The MONEI API is organized around [REST](https://en.wikipedia.org/wiki/Representational_State_Transfer) principles. Our API is designed to be intuitive and developer-friendly.  ### Base URL  All API requests should be made to:  ``` https://api.monei.com/v1 ```  ### Environment  MONEI provides two environments:  - **Test Environment**: For development and testing without processing real payments - **Live Environment**: For processing real transactions in production  ### Client Libraries  We provide official SDKs to simplify integration:  - [PHP SDK](https://github.com/MONEI/monei-php-sdk) - [Python SDK](https://github.com/MONEI/monei-python-sdk) - [Node.js SDK](https://github.com/MONEI/monei-node-sdk) - [Postman Collection](https://postman.monei.com/)  Our SDKs handle authentication, error handling, and request formatting automatically.  You can download the OpenAPI specification from the https://js.monei.com/api/v1/openapi.json and generate your own client library using the [OpenAPI Generator](https://openapi-generator.tech/).  ### Important Requirements  - All API requests must be made over HTTPS - If you are not using our official SDKs, you **must provide a valid `User-Agent` header** with each request - Requests without proper authentication will return a `401 Unauthorized` error  ### Error Handling  The API returns consistent error codes and messages to help you troubleshoot issues. Each response includes a `statusCode` attribute indicating the outcome of your request.  ### Rate Limits  The API implements rate limiting to ensure stability. If you exceed the limits, requests will return a `429 Too Many Requests` status code.  # Authentication  <!-- Redoc-Inject: <security-definitions> -->
 *
 * The version of the OpenAPI document: 1.5.8
 * Generated by: https://openapi-generator.tech
 * OpenAPI Generator version: 6.0.1
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Please update the test case below to test the model.
 */

namespace Monei\Test\Model;

use PHPUnit\Framework\TestCase;

/**
 * PaymentMethodsMetadataClickToPayTest Class Doc Comment
 *
 * @category    Class
 * @description PaymentMethodsMetadataClickToPay
 * @package     Monei
 * @author      OpenAPI Generator team
 * @link        https://openapi-generator.tech
 */
class PaymentMethodsMetadataClickToPayTest extends TestCase
{
    /**
     * Setup before running any test case
     */
    public static function setUpBeforeClass(): void
    {
    }

    /**
     * Setup before running each test case
     */
    public function setUp(): void
    {
    }

    /**
     * Clean up after running each test case
     */
    public function tearDown(): void
    {
    }

    /**
     * Clean up after running all test cases
     */
    public static function tearDownAfterClass(): void
    {
    }

    /**
     * Test "PaymentMethodsMetadataClickToPay"
     */
    public function testPaymentMethodsMetadataClickToPay()
    {
        $model = new \Monei\Model\PaymentMethodsMetadataClickToPay();
        $this->assertInstanceOf(\Monei\Model\PaymentMethodsMetadataClickToPay::class, $model);

        // Test with constructor parameters
        $data = [
            'token_support' => true,
            'discover' => new \Monei\Model\PaymentMethodsMetadataClickToPayDiscover(),
            'mastercard' => new \Monei\Model\PaymentMethodsMetadataClickToPayMastercard(),
            'visa' => new \Monei\Model\PaymentMethodsMetadataClickToPayVisa(),
        ];

        $model = new \Monei\Model\PaymentMethodsMetadataClickToPay($data);
        $this->assertEquals($data['token_support'], $model->getTokenSupport());
        $this->assertEquals($data['discover'], $model->getDiscover());
        $this->assertEquals($data['mastercard'], $model->getMastercard());
        $this->assertEquals($data['visa'], $model->getVisa());
    }

    /**
     * Test attribute "token_support"
     */
    public function testPropertyTokenSupport()
    {
        $model = new \Monei\Model\PaymentMethodsMetadataClickToPay();
        $expected = true;
        $model->setTokenSupport($expected);
        $this->assertEquals($expected, $model->getTokenSupport());
    }

    /**
     * Test attribute "discover"
     */
    public function testPropertyDiscover()
    {
        $model = new \Monei\Model\PaymentMethodsMetadataClickToPay();

        // Test with null value
        $this->assertNull($model->getDiscover());

        // Test with PaymentMethodsMetadataClickToPayDiscover object
        $expected = new \Monei\Model\PaymentMethodsMetadataClickToPayDiscover();

        $model->setDiscover($expected);
        $this->assertInstanceOf(\Monei\Model\PaymentMethodsMetadataClickToPayDiscover::class, $model->getDiscover());
    }

    /**
     * Test attribute "mastercard"
     */
    public function testPropertyMastercard()
    {
        $model = new \Monei\Model\PaymentMethodsMetadataClickToPay();

        // Test with null value
        $this->assertNull($model->getMastercard());

        // Test with PaymentMethodsMetadataClickToPayMastercard object
        $expected = new \Monei\Model\PaymentMethodsMetadataClickToPayMastercard();

        $model->setMastercard($expected);
        $this->assertInstanceOf(\Monei\Model\PaymentMethodsMetadataClickToPayMastercard::class, $model->getMastercard());
    }

    /**
     * Test attribute "visa"
     */
    public function testPropertyVisa()
    {
        $model = new \Monei\Model\PaymentMethodsMetadataClickToPay();

        // Test with null value
        $this->assertNull($model->getVisa());

        // Test with PaymentMethodsMetadataClickToPayVisa object
        $expected = new \Monei\Model\PaymentMethodsMetadataClickToPayVisa();

        $model->setVisa($expected);
        $this->assertInstanceOf(\Monei\Model\PaymentMethodsMetadataClickToPayVisa::class, $model->getVisa());
    }
}
