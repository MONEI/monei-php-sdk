<?php

/**
 * SendPaymentLinkRequestTest
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
 * The version of the OpenAPI document: 1.5.8
 * Generated by: https://openapi-generator.tech
 * OpenAPI Generator version: 6.0.1
 */

namespace Monei\Test\Model;

use PHPUnit\Framework\TestCase;

/**
 * SendPaymentLinkRequestTest Class Doc Comment
 *
 * @category    Class
 * @description SendPaymentLinkRequest
 * @package     Monei
 * @author      OpenAPI Generator team
 * @link        https://openapi-generator.tech
 */
class SendPaymentLinkRequestTest extends TestCase
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
     * Test "SendPaymentLinkRequest"
     */
    public function testSendPaymentLinkRequest()
    {
        $model = new \Monei\Model\SendPaymentLinkRequest();
        $this->assertInstanceOf(\Monei\Model\SendPaymentLinkRequest::class, $model);
    }

}