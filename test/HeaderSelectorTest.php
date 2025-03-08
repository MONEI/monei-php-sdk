<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Monei\HeaderSelector;

class HeaderSelectorTest extends TestCase
{
    private $headerSelector;

    protected function setUp(): void
    {
        $this->headerSelector = new HeaderSelector();
    }

    public function testSelectHeadersWithEmptyAccept()
    {
        $headers = $this->headerSelector->selectHeaders([], ['application/xml']);
        $this->assertArrayNotHasKey('Accept', $headers);
        $this->assertEquals('application/xml', $headers['Content-Type']);
    }

    public function testSelectHeadersWithEmptyContentType()
    {
        $headers = $this->headerSelector->selectHeaders(['application/json'], []);
        $this->assertEquals('application/json', $headers['Accept']);
        $this->assertEquals('application/json', $headers['Content-Type']);
    }

    public function testSelectHeadersWithJsonAccept()
    {
        $headers = $this->headerSelector->selectHeaders(['application/json'], ['application/xml']);
        $this->assertEquals('application/json', $headers['Accept']);
        $this->assertEquals('application/xml', $headers['Content-Type']);
    }

    public function testSelectHeadersWithMultipleAccept()
    {
        $headers = $this->headerSelector->selectHeaders(['application/json', 'application/xml'], ['application/xml']);
        $this->assertEquals('application/json', $headers['Accept']);
        $this->assertEquals('application/xml', $headers['Content-Type']);
    }

    public function testSelectHeadersWithJsonContentType()
    {
        $headers = $this->headerSelector->selectHeaders(['application/xml'], ['application/json']);
        $this->assertEquals('application/xml', $headers['Accept']);
        $this->assertEquals('application/json', $headers['Content-Type']);
    }

    public function testSelectHeadersWithMultipleContentTypes()
    {
        $headers = $this->headerSelector->selectHeaders(['application/xml'], ['application/json', 'application/xml']);
        $this->assertEquals('application/xml', $headers['Accept']);
        $this->assertEquals('application/json', $headers['Content-Type']);
    }

    public function testSelectHeadersForMultipart()
    {
        $headers = $this->headerSelector->selectHeadersForMultipart(['application/json']);
        $this->assertEquals('application/json', $headers['Accept']);
        $this->assertArrayNotHasKey('Content-Type', $headers);
    }

    public function testSelectHeadersForMultipartWithEmptyAccept()
    {
        $headers = $this->headerSelector->selectHeadersForMultipart([]);
        $this->assertArrayNotHasKey('Accept', $headers);
        $this->assertArrayNotHasKey('Content-Type', $headers);
    }

    public function testSelectHeadersWithJsonVariant()
    {
        $headers = $this->headerSelector->selectHeaders(['application/vnd.api+json'], ['application/xml']);
        $this->assertEquals('application/vnd.api+json', $headers['Accept']);
        $this->assertEquals('application/xml', $headers['Content-Type']);
    }

    public function testSelectContentTypeDefaultsToJson()
    {
        $headers = $this->headerSelector->selectHeaders(['application/xml'], []);
        $this->assertEquals('application/xml', $headers['Accept']);
        $this->assertEquals('application/json', $headers['Content-Type']);
    }
}
