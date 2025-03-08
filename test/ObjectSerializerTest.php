<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Monei\ObjectSerializer;
use Monei\Model\Payment;
use Monei\Model\PaymentStatus;
use DateTime;

class ObjectSerializerTest extends TestCase
{
    public function testSetDateTimeFormat()
    {
        // Save original format
        $reflectionClass = new \ReflectionClass(ObjectSerializer::class);
        $reflectionProperty = $reflectionClass->getProperty('dateTimeFormat');
        $reflectionProperty->setAccessible(true);
        $originalFormat = $reflectionProperty->getValue();

        // Test setting a new format
        ObjectSerializer::setDateTimeFormat('Y-m-d');
        $this->assertEquals('Y-m-d', $reflectionProperty->getValue());

        // Restore original format
        ObjectSerializer::setDateTimeFormat($originalFormat);
    }

    public function testSanitizeFilename()
    {
        $filename = 'test/file.txt';
        $sanitized = ObjectSerializer::sanitizeFilename($filename);
        $this->assertEquals('file.txt', $sanitized);
    }

    public function testSanitizeTimestamp()
    {
        $timestamp = '2023-01-01T12:00:00.123456789Z';
        $sanitized = ObjectSerializer::sanitizeTimestamp($timestamp);
        $this->assertEquals('2023-01-01T12:00:00.123456Z', $sanitized);
    }

    public function testToPathValue()
    {
        $value = 'test value';
        $pathValue = ObjectSerializer::toPathValue($value);
        $this->assertEquals('test%20value', $pathValue);
    }

    public function testToHeaderValue()
    {
        $value = 'test';
        $headerValue = ObjectSerializer::toHeaderValue($value);
        $this->assertEquals('test', $headerValue);
    }

    public function testToFormValue()
    {
        $value = 'test';
        $formValue = ObjectSerializer::toFormValue($value);
        $this->assertEquals('test', $formValue);
    }

    public function testToString()
    {
        $date = new DateTime('2023-01-01T12:00:00Z');
        $string = ObjectSerializer::toString($date);
        $this->assertEquals($date->format(DateTime::ATOM), $string);
    }

    public function testConvertBoolToQueryStringFormat()
    {
        $this->assertEquals(1, ObjectSerializer::convertBoolToQueryStringFormat(true));
        $this->assertEquals(0, ObjectSerializer::convertBoolToQueryStringFormat(false));
    }

    public function testSerializeCollection()
    {
        $collection = ['item1', 'item2', 'item3'];

        // Test 'csv' style
        $csv = ObjectSerializer::serializeCollection($collection, 'csv');
        $this->assertEquals('item1,item2,item3', $csv);

        // Test 'ssv' style
        $ssv = ObjectSerializer::serializeCollection($collection, 'ssv');
        $this->assertEquals('item1 item2 item3', $ssv);

        // Test 'tsv' style
        $tsv = ObjectSerializer::serializeCollection($collection, 'tsv');
        $this->assertEquals("item1\titem2\titem3", $tsv);

        // Test 'pipes' style
        $pipes = ObjectSerializer::serializeCollection($collection, 'pipes');
        $this->assertEquals('item1|item2|item3', $pipes);
    }

    public function testToQueryValueWithString()
    {
        $result = ObjectSerializer::toQueryValue('test', 'param', 'string');
        $this->assertEquals(['param' => 'test'], $result);
    }

    public function testToQueryValueWithArray()
    {
        $result = ObjectSerializer::toQueryValue(['item1', 'item2'], 'param', 'array', 'form', true);
        $this->assertEquals(['param' => ['item1', 'item2']], $result);
    }

    public function testToQueryValueWithObject()
    {
        $obj = new \stdClass();
        $obj->prop1 = 'value1';
        $obj->prop2 = 'value2';

        $result = ObjectSerializer::toQueryValue($obj, 'param', 'object', 'form', true);
        $this->assertEquals(['prop1' => 'value1', 'prop2' => 'value2'], $result);
    }

    public function testBuildQuery()
    {
        $data = [
            'param1' => 'value1',
            'param2' => ['subvalue1', 'subvalue2'],
            'param3' => true
        ];

        $query = ObjectSerializer::buildQuery($data);
        $this->assertEquals('param1=value1&param2=subvalue1&param2=subvalue2&param3=1', $query);
    }

    public function testSanitizeForSerializationWithScalar()
    {
        $result = ObjectSerializer::sanitizeForSerialization('test');
        $this->assertEquals('test', $result);
    }

    public function testSanitizeForSerializationWithNull()
    {
        $result = ObjectSerializer::sanitizeForSerialization(null);
        $this->assertNull($result);
    }

    public function testSanitizeForSerializationWithArray()
    {
        $data = ['item1', 'item2', null];
        $result = ObjectSerializer::sanitizeForSerialization($data);
        $this->assertEquals(['item1', 'item2', null], $result);
    }

    public function testSanitizeForSerializationWithDateTime()
    {
        $date = new DateTime('2023-01-01T12:00:00Z');
        $result = ObjectSerializer::sanitizeForSerialization($date);
        $this->assertEquals($date->format(DateTime::ATOM), $result);
    }

    public function testSanitizeForSerializationWithObject()
    {
        $obj = new \stdClass();
        $obj->prop1 = 'value1';
        $obj->prop2 = null;

        $result = ObjectSerializer::sanitizeForSerialization($obj);
        $this->assertIsObject($result);
        $this->assertEquals('value1', $result->prop1);
        $this->assertNull($result->prop2);
    }

    public function testDeserializeWithNull()
    {
        $result = ObjectSerializer::deserialize(null, 'string');
        $this->assertNull($result);
    }

    public function testDeserializeWithString()
    {
        $result = ObjectSerializer::deserialize('test', 'string');
        $this->assertEquals('test', $result);
    }

    public function testDeserializeWithInteger()
    {
        $result = ObjectSerializer::deserialize('123', 'int');
        $this->assertEquals(123, $result);
    }

    public function testDeserializeWithFloat()
    {
        $result = ObjectSerializer::deserialize('123.45', 'float');
        $this->assertEquals(123.45, $result);
    }

    public function testDeserializeWithBoolean()
    {
        $result = ObjectSerializer::deserialize('true', 'bool');
        $this->assertTrue($result);
    }

    public function testDeserializeWithArray()
    {
        $data = ['item1', 'item2'];
        $result = ObjectSerializer::deserialize($data, 'array');
        $this->assertEquals($data, $result);
    }

    public function testDeserializeWithDateTime()
    {
        $result = ObjectSerializer::deserialize('2023-01-01T12:00:00Z', '\DateTime');
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-01-01T12:00:00+00:00', $result->format(DateTime::ATOM));
    }

    public function testDeserializeWithEnum()
    {
        $result = ObjectSerializer::deserialize('SUCCEEDED', PaymentStatus::class);
        $this->assertEquals(PaymentStatus::SUCCEEDED, $result);
    }
}
