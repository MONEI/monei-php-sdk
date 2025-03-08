<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Monei\Configuration;

class ConfigurationTest extends TestCase
{
    private $config;

    protected function setUp(): void
    {
        $this->config = new Configuration();
    }

    public function testGetDefaultConfiguration()
    {
        $defaultConfig = Configuration::getDefaultConfiguration();
        $this->assertInstanceOf(Configuration::class, $defaultConfig);
    }

    public function testSetApiKey()
    {
        $this->config->setApiKey('Authorization', 'test_api_key');
        $this->assertEquals('test_api_key', $this->config->getApiKey('Authorization'));
    }

    public function testSetApiKeyPrefix()
    {
        $this->config->setApiKeyPrefix('Authorization', 'Bearer');
        $this->assertEquals('Bearer', $this->config->getApiKeyPrefix('Authorization'));
    }

    public function testGetApiKeyWithPrefix()
    {
        $this->config->setApiKey('Authorization', 'test_api_key');
        $this->config->setApiKeyPrefix('Authorization', 'Bearer');
        $this->assertEquals('Bearer test_api_key', $this->config->getApiKeyWithPrefix('Authorization'));
    }

    public function testGetApiKeyWithPrefixNoPrefix()
    {
        $this->config->setApiKey('Authorization', 'test_api_key');
        $this->assertEquals('test_api_key', $this->config->getApiKeyWithPrefix('Authorization'));
    }

    public function testGetApiKeyWithPrefixNoKey()
    {
        $this->config->setApiKeyPrefix('Authorization', 'Bearer');
        $this->assertEquals('', $this->config->getApiKeyWithPrefix('Authorization'));
    }

    public function testSetUserAgent()
    {
        $userAgent = 'TestApp/1.0.0';
        $this->config->setUserAgent($userAgent);
        $this->assertEquals($userAgent, $this->config->getUserAgent());
    }

    public function testGetHostWithTrailingSlash()
    {
        $this->config->setHost('https://api.example.com/');
        $this->assertEquals('https://api.example.com/', $this->config->getHost());
    }

    public function testGetHostWithoutTrailingSlash()
    {
        $this->config->setHost('https://api.example.com');
        $this->assertEquals('https://api.example.com', $this->config->getHost());
    }

    public function testSetDebug()
    {
        $this->config->setDebug(true);
        $this->assertTrue($this->config->getDebug());
    }

    public function testSetTempFolderPath()
    {
        $tempPath = '/tmp/test';
        $this->config->setTempFolderPath($tempPath);
        $this->assertEquals($tempPath, $this->config->getTempFolderPath());
    }

    public function testToDebugReport()
    {
        $this->config->setHost('https://api.example.com');
        $this->config->setUserAgent('TestApp/1.0.0');
        $this->config->setDebug(true);
        $this->config->setApiKey('Authorization', 'test_api_key');

        $report = Configuration::toDebugReport();

        $this->assertIsString($report);
        $this->assertStringContainsString('PHP Version: ' . PHP_VERSION, $report);
    }
}
