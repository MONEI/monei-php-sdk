<?php

/**
 * PaymentPaymentMethodSepa
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
 * The MONEI API is organized around REST principles. Our API is designed to be intuitive and developer-friendly.  ### Base URL  All API requests should be made to:  ``` https://api.monei.com/v1 ```  ### Environment  MONEI provides two environments:  - **Test Environment**: For development and testing without processing real payments - **Live Environment**: For processing real transactions in production  ### Client Libraries  We provide official SDKs to simplify integration:  - [PHP SDK](https://github.com/MONEI/monei-php-sdk) - [Python SDK](https://github.com/MONEI/monei-python-sdk) - [Node.js SDK](https://github.com/MONEI/monei-node-sdk) - [Postman Collection](https://postman.monei.com/)  Our SDKs handle authentication, error handling, and request formatting automatically.  You can download the OpenAPI specification from the https://js.monei.com/api/v1/openapi.json and generate your own client library using the [OpenAPI Generator](https://openapi-generator.tech/).  ### Important Requirements  - All API requests must be made over HTTPS - If you are not using our official SDKs, you **must provide a valid `User-Agent` header** with each request - Requests without proper authentication will return a `401 Unauthorized` error  ### Error Handling  The API returns consistent error codes and messages to help you troubleshoot issues. Each response includes a `statusCode` attribute indicating the outcome of your request.  ### Rate Limits  The API implements rate limiting to ensure stability. If you exceed the limits, requests will return a `429 Too Many Requests` status code.
 *
 * The version of the OpenAPI document: 1.7.0
 * Generated by: https://openapi-generator.tech
 * OpenAPI Generator version: 6.0.1
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace Monei\Model;

use \ArrayAccess;
use \Monei\ObjectSerializer;

/**
 * PaymentPaymentMethodSepa Class Doc Comment
 *
 * @category Class
 * @description Details from SEPA order used as payment method at the time of the transaction.
 * @package  Monei
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class PaymentPaymentMethodSepa implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'Payment-PaymentMethodSepa';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'accountholder_address' => 'string',
        'accountholder_email' => 'string',
        'accountholder_name' => 'string',
        'country_code' => 'string',
        'bank_address' => 'string',
        'bank_code' => 'string',
        'bank_name' => 'string',
        'bic' => 'string',
        'last4' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'accountholder_address' => null,
        'accountholder_email' => null,
        'accountholder_name' => null,
        'country_code' => null,
        'bank_address' => null,
        'bank_code' => null,
        'bank_name' => null,
        'bic' => null,
        'last4' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'accountholder_address' => 'accountholderAddress',
        'accountholder_email' => 'accountholderEmail',
        'accountholder_name' => 'accountholderName',
        'country_code' => 'countryCode',
        'bank_address' => 'bankAddress',
        'bank_code' => 'bankCode',
        'bank_name' => 'bankName',
        'bic' => 'bic',
        'last4' => 'last4'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'accountholder_address' => 'setAccountholderAddress',
        'accountholder_email' => 'setAccountholderEmail',
        'accountholder_name' => 'setAccountholderName',
        'country_code' => 'setCountryCode',
        'bank_address' => 'setBankAddress',
        'bank_code' => 'setBankCode',
        'bank_name' => 'setBankName',
        'bic' => 'setBic',
        'last4' => 'setLast4'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'accountholder_address' => 'getAccountholderAddress',
        'accountholder_email' => 'getAccountholderEmail',
        'accountholder_name' => 'getAccountholderName',
        'country_code' => 'getCountryCode',
        'bank_address' => 'getBankAddress',
        'bank_code' => 'getBankCode',
        'bank_name' => 'getBankName',
        'bic' => 'getBic',
        'last4' => 'getLast4'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }


    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['accountholder_address'] = $data['accountholder_address'] ?? null;
        $this->container['accountholder_email'] = $data['accountholder_email'] ?? null;
        $this->container['accountholder_name'] = $data['accountholder_name'] ?? null;
        $this->container['country_code'] = $data['country_code'] ?? null;
        $this->container['bank_address'] = $data['bank_address'] ?? null;
        $this->container['bank_code'] = $data['bank_code'] ?? null;
        $this->container['bank_name'] = $data['bank_name'] ?? null;
        $this->container['bic'] = $data['bic'] ?? null;
        $this->container['last4'] = $data['last4'] ?? null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets accountholder_address
     *
     * @return string|null
     */
    public function getAccountholderAddress()
    {
        return $this->container['accountholder_address'];
    }

    /**
     * Sets accountholder_address
     *
     * @param string|null $accountholder_address The address of the account holder.
     *
     * @return self
     */
    public function setAccountholderAddress($accountholder_address)
    {
        $this->container['accountholder_address'] = $accountholder_address;

        return $this;
    }

    /**
     * Gets accountholder_email
     *
     * @return string|null
     */
    public function getAccountholderEmail()
    {
        return $this->container['accountholder_email'];
    }

    /**
     * Sets accountholder_email
     *
     * @param string|null $accountholder_email The email of the account holder.
     *
     * @return self
     */
    public function setAccountholderEmail($accountholder_email)
    {
        $this->container['accountholder_email'] = $accountholder_email;

        return $this;
    }

    /**
     * Gets accountholder_name
     *
     * @return string|null
     */
    public function getAccountholderName()
    {
        return $this->container['accountholder_name'];
    }

    /**
     * Sets accountholder_name
     *
     * @param string|null $accountholder_name The name of the account holder.
     *
     * @return self
     */
    public function setAccountholderName($accountholder_name)
    {
        $this->container['accountholder_name'] = $accountholder_name;

        return $this;
    }

    /**
     * Gets country_code
     *
     * @return string|null
     */
    public function getCountryCode()
    {
        return $this->container['country_code'];
    }

    /**
     * Sets country_code
     *
     * @param string|null $country_code The country code of the account holder.
     *
     * @return self
     */
    public function setCountryCode($country_code)
    {
        $this->container['country_code'] = $country_code;

        return $this;
    }

    /**
     * Gets bank_address
     *
     * @return string|null
     */
    public function getBankAddress()
    {
        return $this->container['bank_address'];
    }

    /**
     * Sets bank_address
     *
     * @param string|null $bank_address The address of the bank.
     *
     * @return self
     */
    public function setBankAddress($bank_address)
    {
        $this->container['bank_address'] = $bank_address;

        return $this;
    }

    /**
     * Gets bank_code
     *
     * @return string|null
     */
    public function getBankCode()
    {
        return $this->container['bank_code'];
    }

    /**
     * Sets bank_code
     *
     * @param string|null $bank_code The code of the bank.
     *
     * @return self
     */
    public function setBankCode($bank_code)
    {
        $this->container['bank_code'] = $bank_code;

        return $this;
    }

    /**
     * Gets bank_name
     *
     * @return string|null
     */
    public function getBankName()
    {
        return $this->container['bank_name'];
    }

    /**
     * Sets bank_name
     *
     * @param string|null $bank_name The name of the bank.
     *
     * @return self
     */
    public function setBankName($bank_name)
    {
        $this->container['bank_name'] = $bank_name;

        return $this;
    }

    /**
     * Gets bic
     *
     * @return string|null
     */
    public function getBic()
    {
        return $this->container['bic'];
    }

    /**
     * Sets bic
     *
     * @param string|null $bic The BIC of the bank.
     *
     * @return self
     */
    public function setBic($bic)
    {
        $this->container['bic'] = $bic;

        return $this;
    }

    /**
     * Gets last4
     *
     * @return string|null
     */
    public function getLast4()
    {
        return $this->container['last4'];
    }

    /**
     * Sets last4
     *
     * @param string|null $last4 The last 4 digits of the IBAN.
     *
     * @return self
     */
    public function setLast4($last4)
    {
        $this->container['last4'] = $last4;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param int|null $offset Offset
     * @param mixed    $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     * of any type other than a resource.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Gets a header-safe presentation of the object
     *
     * @return string
     */
    public function toHeaderValue()
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


