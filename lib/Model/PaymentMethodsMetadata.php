<?php

/**
 * PaymentMethodsMetadata
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
 * PaymentMethodsMetadata Class Doc Comment
 *
 * @category Class
 * @description Additional configuration details for each payment method.
 * @package  Monei
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class PaymentMethodsMetadata implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'PaymentMethods-Metadata';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'alipay' => '\Monei\Model\PaymentMethodsMetadataAlipay',
        'bancontact' => '\Monei\Model\PaymentMethodsMetadataBancontact',
        'bizum' => '\Monei\Model\PaymentMethodsMetadataBizum',
        'blik' => '\Monei\Model\PaymentMethodsMetadataBlik',
        'card' => '\Monei\Model\PaymentMethodsMetadataCard',
        'eps' => '\Monei\Model\PaymentMethodsMetadataEps',
        'i_deal' => '\Monei\Model\PaymentMethodsMetadataIDeal',
        'mbway' => '\Monei\Model\PaymentMethodsMetadataMbway',
        'multibanco' => '\Monei\Model\PaymentMethodsMetadataMbway',
        'sofort' => '\Monei\Model\PaymentMethodsMetadataSofort',
        'trustly' => '\Monei\Model\PaymentMethodsMetadataTrustly',
        'sepa' => '\Monei\Model\PaymentMethodsMetadataSepa',
        'klarna' => '\Monei\Model\PaymentMethodsMetadataKlarna',
        'giropay' => '\Monei\Model\PaymentMethodsMetadataGiropay',
        'google_pay' => '\Monei\Model\PaymentMethodsMetadataGooglePay',
        'apple_pay' => '\Monei\Model\PaymentMethodsMetadataApplePay',
        'click_to_pay' => '\Monei\Model\PaymentMethodsMetadataClickToPay'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'alipay' => null,
        'bancontact' => null,
        'bizum' => null,
        'blik' => null,
        'card' => null,
        'eps' => null,
        'i_deal' => null,
        'mbway' => null,
        'multibanco' => null,
        'sofort' => null,
        'trustly' => null,
        'sepa' => null,
        'klarna' => null,
        'giropay' => null,
        'google_pay' => null,
        'apple_pay' => null,
        'click_to_pay' => null
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
        'alipay' => 'alipay',
        'bancontact' => 'bancontact',
        'bizum' => 'bizum',
        'blik' => 'blik',
        'card' => 'card',
        'eps' => 'eps',
        'i_deal' => 'iDeal',
        'mbway' => 'mbway',
        'multibanco' => 'multibanco',
        'sofort' => 'sofort',
        'trustly' => 'trustly',
        'sepa' => 'sepa',
        'klarna' => 'klarna',
        'giropay' => 'giropay',
        'google_pay' => 'googlePay',
        'apple_pay' => 'applePay',
        'click_to_pay' => 'clickToPay'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'alipay' => 'setAlipay',
        'bancontact' => 'setBancontact',
        'bizum' => 'setBizum',
        'blik' => 'setBlik',
        'card' => 'setCard',
        'eps' => 'setEps',
        'i_deal' => 'setIDeal',
        'mbway' => 'setMbway',
        'multibanco' => 'setMultibanco',
        'sofort' => 'setSofort',
        'trustly' => 'setTrustly',
        'sepa' => 'setSepa',
        'klarna' => 'setKlarna',
        'giropay' => 'setGiropay',
        'google_pay' => 'setGooglePay',
        'apple_pay' => 'setApplePay',
        'click_to_pay' => 'setClickToPay'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'alipay' => 'getAlipay',
        'bancontact' => 'getBancontact',
        'bizum' => 'getBizum',
        'blik' => 'getBlik',
        'card' => 'getCard',
        'eps' => 'getEps',
        'i_deal' => 'getIDeal',
        'mbway' => 'getMbway',
        'multibanco' => 'getMultibanco',
        'sofort' => 'getSofort',
        'trustly' => 'getTrustly',
        'sepa' => 'getSepa',
        'klarna' => 'getKlarna',
        'giropay' => 'getGiropay',
        'google_pay' => 'getGooglePay',
        'apple_pay' => 'getApplePay',
        'click_to_pay' => 'getClickToPay'
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
        $this->container['alipay'] = $data['alipay'] ?? null;
        $this->container['bancontact'] = $data['bancontact'] ?? null;
        $this->container['bizum'] = $data['bizum'] ?? null;
        $this->container['blik'] = $data['blik'] ?? null;
        $this->container['card'] = $data['card'] ?? null;
        $this->container['eps'] = $data['eps'] ?? null;
        $this->container['i_deal'] = $data['i_deal'] ?? null;
        $this->container['mbway'] = $data['mbway'] ?? null;
        $this->container['multibanco'] = $data['multibanco'] ?? null;
        $this->container['sofort'] = $data['sofort'] ?? null;
        $this->container['trustly'] = $data['trustly'] ?? null;
        $this->container['sepa'] = $data['sepa'] ?? null;
        $this->container['klarna'] = $data['klarna'] ?? null;
        $this->container['giropay'] = $data['giropay'] ?? null;
        $this->container['google_pay'] = $data['google_pay'] ?? null;
        $this->container['apple_pay'] = $data['apple_pay'] ?? null;
        $this->container['click_to_pay'] = $data['click_to_pay'] ?? null;
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
     * Gets alipay
     *
     * @return \Monei\Model\PaymentMethodsMetadataAlipay|null
     */
    public function getAlipay()
    {
        return $this->container['alipay'];
    }

    /**
     * Sets alipay
     *
     * @param \Monei\Model\PaymentMethodsMetadataAlipay|null $alipay alipay
     *
     * @return self
     */
    public function setAlipay($alipay)
    {
        $this->container['alipay'] = $alipay;

        return $this;
    }

    /**
     * Gets bancontact
     *
     * @return \Monei\Model\PaymentMethodsMetadataBancontact|null
     */
    public function getBancontact()
    {
        return $this->container['bancontact'];
    }

    /**
     * Sets bancontact
     *
     * @param \Monei\Model\PaymentMethodsMetadataBancontact|null $bancontact bancontact
     *
     * @return self
     */
    public function setBancontact($bancontact)
    {
        $this->container['bancontact'] = $bancontact;

        return $this;
    }

    /**
     * Gets bizum
     *
     * @return \Monei\Model\PaymentMethodsMetadataBizum|null
     */
    public function getBizum()
    {
        return $this->container['bizum'];
    }

    /**
     * Sets bizum
     *
     * @param \Monei\Model\PaymentMethodsMetadataBizum|null $bizum bizum
     *
     * @return self
     */
    public function setBizum($bizum)
    {
        $this->container['bizum'] = $bizum;

        return $this;
    }

    /**
     * Gets blik
     *
     * @return \Monei\Model\PaymentMethodsMetadataBlik|null
     */
    public function getBlik()
    {
        return $this->container['blik'];
    }

    /**
     * Sets blik
     *
     * @param \Monei\Model\PaymentMethodsMetadataBlik|null $blik blik
     *
     * @return self
     */
    public function setBlik($blik)
    {
        $this->container['blik'] = $blik;

        return $this;
    }

    /**
     * Gets card
     *
     * @return \Monei\Model\PaymentMethodsMetadataCard|null
     */
    public function getCard()
    {
        return $this->container['card'];
    }

    /**
     * Sets card
     *
     * @param \Monei\Model\PaymentMethodsMetadataCard|null $card card
     *
     * @return self
     */
    public function setCard($card)
    {
        $this->container['card'] = $card;

        return $this;
    }

    /**
     * Gets eps
     *
     * @return \Monei\Model\PaymentMethodsMetadataEps|null
     */
    public function getEps()
    {
        return $this->container['eps'];
    }

    /**
     * Sets eps
     *
     * @param \Monei\Model\PaymentMethodsMetadataEps|null $eps eps
     *
     * @return self
     */
    public function setEps($eps)
    {
        $this->container['eps'] = $eps;

        return $this;
    }

    /**
     * Gets i_deal
     *
     * @return \Monei\Model\PaymentMethodsMetadataIDeal|null
     */
    public function getIDeal()
    {
        return $this->container['i_deal'];
    }

    /**
     * Sets i_deal
     *
     * @param \Monei\Model\PaymentMethodsMetadataIDeal|null $i_deal i_deal
     *
     * @return self
     */
    public function setIDeal($i_deal)
    {
        $this->container['i_deal'] = $i_deal;

        return $this;
    }

    /**
     * Gets mbway
     *
     * @return \Monei\Model\PaymentMethodsMetadataMbway|null
     */
    public function getMbway()
    {
        return $this->container['mbway'];
    }

    /**
     * Sets mbway
     *
     * @param \Monei\Model\PaymentMethodsMetadataMbway|null $mbway mbway
     *
     * @return self
     */
    public function setMbway($mbway)
    {
        $this->container['mbway'] = $mbway;

        return $this;
    }

    /**
     * Gets multibanco
     *
     * @return \Monei\Model\PaymentMethodsMetadataMbway|null
     */
    public function getMultibanco()
    {
        return $this->container['multibanco'];
    }

    /**
     * Sets multibanco
     *
     * @param \Monei\Model\PaymentMethodsMetadataMbway|null $multibanco multibanco
     *
     * @return self
     */
    public function setMultibanco($multibanco)
    {
        $this->container['multibanco'] = $multibanco;

        return $this;
    }

    /**
     * Gets sofort
     *
     * @return \Monei\Model\PaymentMethodsMetadataSofort|null
     */
    public function getSofort()
    {
        return $this->container['sofort'];
    }

    /**
     * Sets sofort
     *
     * @param \Monei\Model\PaymentMethodsMetadataSofort|null $sofort sofort
     *
     * @return self
     */
    public function setSofort($sofort)
    {
        $this->container['sofort'] = $sofort;

        return $this;
    }

    /**
     * Gets trustly
     *
     * @return \Monei\Model\PaymentMethodsMetadataTrustly|null
     */
    public function getTrustly()
    {
        return $this->container['trustly'];
    }

    /**
     * Sets trustly
     *
     * @param \Monei\Model\PaymentMethodsMetadataTrustly|null $trustly trustly
     *
     * @return self
     */
    public function setTrustly($trustly)
    {
        $this->container['trustly'] = $trustly;

        return $this;
    }

    /**
     * Gets sepa
     *
     * @return \Monei\Model\PaymentMethodsMetadataSepa|null
     */
    public function getSepa()
    {
        return $this->container['sepa'];
    }

    /**
     * Sets sepa
     *
     * @param \Monei\Model\PaymentMethodsMetadataSepa|null $sepa sepa
     *
     * @return self
     */
    public function setSepa($sepa)
    {
        $this->container['sepa'] = $sepa;

        return $this;
    }

    /**
     * Gets klarna
     *
     * @return \Monei\Model\PaymentMethodsMetadataKlarna|null
     */
    public function getKlarna()
    {
        return $this->container['klarna'];
    }

    /**
     * Sets klarna
     *
     * @param \Monei\Model\PaymentMethodsMetadataKlarna|null $klarna klarna
     *
     * @return self
     */
    public function setKlarna($klarna)
    {
        $this->container['klarna'] = $klarna;

        return $this;
    }

    /**
     * Gets giropay
     *
     * @return \Monei\Model\PaymentMethodsMetadataGiropay|null
     */
    public function getGiropay()
    {
        return $this->container['giropay'];
    }

    /**
     * Sets giropay
     *
     * @param \Monei\Model\PaymentMethodsMetadataGiropay|null $giropay giropay
     *
     * @return self
     */
    public function setGiropay($giropay)
    {
        $this->container['giropay'] = $giropay;

        return $this;
    }

    /**
     * Gets google_pay
     *
     * @return \Monei\Model\PaymentMethodsMetadataGooglePay|null
     */
    public function getGooglePay()
    {
        return $this->container['google_pay'];
    }

    /**
     * Sets google_pay
     *
     * @param \Monei\Model\PaymentMethodsMetadataGooglePay|null $google_pay google_pay
     *
     * @return self
     */
    public function setGooglePay($google_pay)
    {
        $this->container['google_pay'] = $google_pay;

        return $this;
    }

    /**
     * Gets apple_pay
     *
     * @return \Monei\Model\PaymentMethodsMetadataApplePay|null
     */
    public function getApplePay()
    {
        return $this->container['apple_pay'];
    }

    /**
     * Sets apple_pay
     *
     * @param \Monei\Model\PaymentMethodsMetadataApplePay|null $apple_pay apple_pay
     *
     * @return self
     */
    public function setApplePay($apple_pay)
    {
        $this->container['apple_pay'] = $apple_pay;

        return $this;
    }

    /**
     * Gets click_to_pay
     *
     * @return \Monei\Model\PaymentMethodsMetadataClickToPay|null
     */
    public function getClickToPay()
    {
        return $this->container['click_to_pay'];
    }

    /**
     * Sets click_to_pay
     *
     * @param \Monei\Model\PaymentMethodsMetadataClickToPay|null $click_to_pay click_to_pay
     *
     * @return self
     */
    public function setClickToPay($click_to_pay)
    {
        $this->container['click_to_pay'] = $click_to_pay;

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


