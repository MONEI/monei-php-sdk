<?php

namespace Tests;

use Monei\Configuration;
use Monei\MoneiClient;
use PHPUnit\Framework\TestCase;

class MoneiClientUserAgentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset the default configuration to ensure test isolation
        Configuration::setDefaultConfiguration(new Configuration());
    }

    /**
     * Test setting user agent via config before initialization
     */
    public function testSetUserAgentViaConfig()
    {
        $config = new Configuration();
        $config->setUserAgent('MONEI/MyPlatform/1.0.0');

        $client = new MoneiClient('test_api_key', $config);

        $this->assertEquals('MONEI/MyPlatform/1.0.0', $client->getConfig()->getUserAgent());
    }

    /**
     * Test setting user agent via instance after initialization
     */
    public function testSetUserAgentViaInstance()
    {
        $client = new MoneiClient('test_api_key');
        $client->setUserAgent('MONEI/MyPlatform/2.0.0');

        $this->assertEquals('MONEI/MyPlatform/2.0.0', $client->getConfig()->getUserAgent());
    }

    /**
     * Test default user agent when no custom agent is set
     */
    public function testDefaultUserAgent()
    {
        $client = new MoneiClient('test_api_key');

        $this->assertEquals(
            MoneiClient::DEFAULT_USER_AGENT . MoneiClient::SDK_VERSION,
            $client->getConfig()->getUserAgent()
        );
    }

    /**
     * Test that config user agent is preserved during initialization
     */
    public function testConfigUserAgentIsPreserved()
    {
        $config = new Configuration();
        $customUserAgent = 'MONEI/CustomPlatform/3.0.0';
        $config->setUserAgent($customUserAgent);

        // Create client with custom config
        $client = new MoneiClient('test_api_key', $config);

        // Verify the custom user agent was not overwritten
        $this->assertEquals($customUserAgent, $client->getConfig()->getUserAgent());

        // Verify we can still change it via instance
        $client->setUserAgent('MONEI/AnotherPlatform/4.0.0');
        $this->assertEquals('MONEI/AnotherPlatform/4.0.0', $client->getConfig()->getUserAgent());
    }

    /**
     * Test that different OpenAPI-Generator versions are handled correctly
     */
    public function testDifferentOpenApiGeneratorVersions()
    {
        // Test with different OpenAPI-Generator versions
        $openApiVersions = [
            'OpenAPI-Generator/1.0.0/PHP',
            'OpenAPI-Generator/2.0.0/PHP',
            'OpenAPI-Generator/5.4.0/PHP',
            'OpenAPI-Generator/6.0.1/PHP',
            'OpenAPI-Generator/PHP',
        ];

        foreach ($openApiVersions as $version) {
            $config = new Configuration();
            // Simulate OpenAPI-Generator default user agent
            $reflection = new \ReflectionClass($config);
            $userAgentProp = $reflection->getProperty('userAgent');
            $userAgentProp->setAccessible(true);
            $userAgentProp->setValue($config, $version);

            $client = new MoneiClient('test_api_key', $config);

            // Should be replaced with MONEI default
            $this->assertEquals(
                MoneiClient::DEFAULT_USER_AGENT . MoneiClient::SDK_VERSION,
                $client->getConfig()->getUserAgent(),
                "Failed for OpenAPI version: $version"
            );
        }
    }
}