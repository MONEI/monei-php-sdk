<?php

/**
 * SendSubscriptionStatusRequestTest
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
 * SendSubscriptionStatusRequestTest Class Doc Comment
 *
 * @category    Class
 * @description SendSubscriptionStatusRequest
 * @package     Monei
 * @author      OpenAPI Generator team
 * @link        https://openapi-generator.tech
 */
class SendSubscriptionStatusRequestTest extends TestCase
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
     * Test "SendSubscriptionStatusRequest"
     */
    public function testSendSubscriptionStatusRequest()
    {
        $model = new \Monei\Model\SendSubscriptionStatusRequest();
        $this->assertInstanceOf(\Monei\Model\SendSubscriptionStatusRequest::class, $model);
    }

}