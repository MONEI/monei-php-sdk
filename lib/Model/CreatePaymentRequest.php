<?php

/**
 * CreatePaymentRequest
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
 * CreatePaymentRequest Class Doc Comment
 *
 * @category Class
 * @package  Monei
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class CreatePaymentRequest implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'CreatePaymentRequest';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'amount' => 'int',
        'currency' => 'string',
        'order_id' => 'string',
        'callback_url' => 'string',
        'complete_url' => 'string',
        'fail_url' => 'string',
        'cancel_url' => 'string',
        'payment_token' => 'string',
        'session_id' => 'string',
        'generate_payment_token' => 'bool',
        'payment_method' => '\Monei\Model\PaymentPaymentMethodInput',
        'allowed_payment_methods' => '\Monei\Model\PaymentPaymentMethods',
        'transaction_type' => '\Monei\Model\PaymentTransactionType',
        'sequence' => '\Monei\Model\PaymentSequence',
        'store_id' => 'string',
        'point_of_sale_id' => 'string',
        'subscription_id' => 'string',
        'auto_recover' => 'bool',
        'description' => 'string',
        'customer' => '\Monei\Model\PaymentCustomer',
        'billing_details' => '\Monei\Model\PaymentBillingDetails',
        'shipping_details' => '\Monei\Model\PaymentShippingDetails',
        'session_details' => '\Monei\Model\PaymentSessionDetails',
        'expire_at' => 'float',
        'metadata' => 'object'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'amount' => 'int32',
        'currency' => null,
        'order_id' => null,
        'callback_url' => null,
        'complete_url' => null,
        'fail_url' => null,
        'cancel_url' => null,
        'payment_token' => null,
        'session_id' => null,
        'generate_payment_token' => null,
        'payment_method' => null,
        'allowed_payment_methods' => null,
        'transaction_type' => null,
        'sequence' => null,
        'store_id' => null,
        'point_of_sale_id' => null,
        'subscription_id' => null,
        'auto_recover' => null,
        'description' => null,
        'customer' => null,
        'billing_details' => null,
        'shipping_details' => null,
        'session_details' => null,
        'expire_at' => 'int64',
        'metadata' => null
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
        'amount' => 'amount',
        'currency' => 'currency',
        'order_id' => 'orderId',
        'callback_url' => 'callbackUrl',
        'complete_url' => 'completeUrl',
        'fail_url' => 'failUrl',
        'cancel_url' => 'cancelUrl',
        'payment_token' => 'paymentToken',
        'session_id' => 'sessionId',
        'generate_payment_token' => 'generatePaymentToken',
        'payment_method' => 'paymentMethod',
        'allowed_payment_methods' => 'allowedPaymentMethods',
        'transaction_type' => 'transactionType',
        'sequence' => 'sequence',
        'store_id' => 'storeId',
        'point_of_sale_id' => 'pointOfSaleId',
        'subscription_id' => 'subscriptionId',
        'auto_recover' => 'autoRecover',
        'description' => 'description',
        'customer' => 'customer',
        'billing_details' => 'billingDetails',
        'shipping_details' => 'shippingDetails',
        'session_details' => 'sessionDetails',
        'expire_at' => 'expireAt',
        'metadata' => 'metadata'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'amount' => 'setAmount',
        'currency' => 'setCurrency',
        'order_id' => 'setOrderId',
        'callback_url' => 'setCallbackUrl',
        'complete_url' => 'setCompleteUrl',
        'fail_url' => 'setFailUrl',
        'cancel_url' => 'setCancelUrl',
        'payment_token' => 'setPaymentToken',
        'session_id' => 'setSessionId',
        'generate_payment_token' => 'setGeneratePaymentToken',
        'payment_method' => 'setPaymentMethod',
        'allowed_payment_methods' => 'setAllowedPaymentMethods',
        'transaction_type' => 'setTransactionType',
        'sequence' => 'setSequence',
        'store_id' => 'setStoreId',
        'point_of_sale_id' => 'setPointOfSaleId',
        'subscription_id' => 'setSubscriptionId',
        'auto_recover' => 'setAutoRecover',
        'description' => 'setDescription',
        'customer' => 'setCustomer',
        'billing_details' => 'setBillingDetails',
        'shipping_details' => 'setShippingDetails',
        'session_details' => 'setSessionDetails',
        'expire_at' => 'setExpireAt',
        'metadata' => 'setMetadata'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'amount' => 'getAmount',
        'currency' => 'getCurrency',
        'order_id' => 'getOrderId',
        'callback_url' => 'getCallbackUrl',
        'complete_url' => 'getCompleteUrl',
        'fail_url' => 'getFailUrl',
        'cancel_url' => 'getCancelUrl',
        'payment_token' => 'getPaymentToken',
        'session_id' => 'getSessionId',
        'generate_payment_token' => 'getGeneratePaymentToken',
        'payment_method' => 'getPaymentMethod',
        'allowed_payment_methods' => 'getAllowedPaymentMethods',
        'transaction_type' => 'getTransactionType',
        'sequence' => 'getSequence',
        'store_id' => 'getStoreId',
        'point_of_sale_id' => 'getPointOfSaleId',
        'subscription_id' => 'getSubscriptionId',
        'auto_recover' => 'getAutoRecover',
        'description' => 'getDescription',
        'customer' => 'getCustomer',
        'billing_details' => 'getBillingDetails',
        'shipping_details' => 'getShippingDetails',
        'session_details' => 'getSessionDetails',
        'expire_at' => 'getExpireAt',
        'metadata' => 'getMetadata'
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
        $this->container['amount'] = $data['amount'] ?? null;
        $this->container['currency'] = $data['currency'] ?? null;
        $this->container['order_id'] = $data['order_id'] ?? null;
        $this->container['callback_url'] = $data['callback_url'] ?? null;
        $this->container['complete_url'] = $data['complete_url'] ?? null;
        $this->container['fail_url'] = $data['fail_url'] ?? null;
        $this->container['cancel_url'] = $data['cancel_url'] ?? null;
        $this->container['payment_token'] = $data['payment_token'] ?? null;
        $this->container['session_id'] = $data['session_id'] ?? null;
        $this->container['generate_payment_token'] = $data['generate_payment_token'] ?? false;
        $this->container['payment_method'] = $data['payment_method'] ?? null;
        $this->container['allowed_payment_methods'] = $data['allowed_payment_methods'] ?? null;
        $this->container['transaction_type'] = $data['transaction_type'] ?? null;
        $this->container['sequence'] = $data['sequence'] ?? null;
        $this->container['store_id'] = $data['store_id'] ?? null;
        $this->container['point_of_sale_id'] = $data['point_of_sale_id'] ?? null;
        $this->container['subscription_id'] = $data['subscription_id'] ?? null;
        $this->container['auto_recover'] = $data['auto_recover'] ?? null;
        $this->container['description'] = $data['description'] ?? null;
        $this->container['customer'] = $data['customer'] ?? null;
        $this->container['billing_details'] = $data['billing_details'] ?? null;
        $this->container['shipping_details'] = $data['shipping_details'] ?? null;
        $this->container['session_details'] = $data['session_details'] ?? null;
        $this->container['expire_at'] = $data['expire_at'] ?? null;
        $this->container['metadata'] = $data['metadata'] ?? null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['amount'] === null) {
            $invalidProperties[] = "'amount' can't be null";
        }
        if ($this->container['currency'] === null) {
            $invalidProperties[] = "'currency' can't be null";
        }
        if ($this->container['order_id'] === null) {
            $invalidProperties[] = "'order_id' can't be null";
        }
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
     * Gets amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->container['amount'];
    }

    /**
     * Sets amount
     *
     * @param int $amount Amount intended to be collected by this payment. A positive integer representing how much to charge in the smallest currency unit (e.g., 100 cents to charge 1.00 USD).
     *
     * @return self
     */
    public function setAmount($amount)
    {
        $this->container['amount'] = $amount;

        return $this;
    }

    /**
     * Gets currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->container['currency'];
    }

    /**
     * Sets currency
     *
     * @param string $currency Three-letter [ISO currency code](https://en.wikipedia.org/wiki/ISO_4217), in uppercase. Must be a supported currency.
     *
     * @return self
     */
    public function setCurrency($currency)
    {
        $this->container['currency'] = $currency;

        return $this;
    }

    /**
     * Gets order_id
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->container['order_id'];
    }

    /**
     * Sets order_id
     *
     * @param string $order_id An order ID from your system. A unique identifier that can be used to reconcile the payment with your internal system.
     *
     * @return self
     */
    public function setOrderId($order_id)
    {
        $this->container['order_id'] = $order_id;

        return $this;
    }

    /**
     * Gets callback_url
     *
     * @return string|null
     */
    public function getCallbackUrl()
    {
        return $this->container['callback_url'];
    }

    /**
     * Sets callback_url
     *
     * @param string|null $callback_url The URL to which a payment result should be sent asynchronously.
     *
     * @return self
     */
    public function setCallbackUrl($callback_url)
    {
        $this->container['callback_url'] = $callback_url;

        return $this;
    }

    /**
     * Gets complete_url
     *
     * @return string|null
     */
    public function getCompleteUrl()
    {
        return $this->container['complete_url'];
    }

    /**
     * Sets complete_url
     *
     * @param string|null $complete_url The URL the customer will be directed to after transaction completed (successful or failed - except if `failUrl` is provided).
     *
     * @return self
     */
    public function setCompleteUrl($complete_url)
    {
        $this->container['complete_url'] = $complete_url;

        return $this;
    }

    /**
     * Gets fail_url
     *
     * @return string|null
     */
    public function getFailUrl()
    {
        return $this->container['fail_url'];
    }

    /**
     * Sets fail_url
     *
     * @param string|null $fail_url The URL the customer will be directed to after transaction has failed, instead of `completeUrl` (used in hosted payment page). This allows to provide two different URLs for successful and failed payments.
     *
     * @return self
     */
    public function setFailUrl($fail_url)
    {
        $this->container['fail_url'] = $fail_url;

        return $this;
    }

    /**
     * Gets cancel_url
     *
     * @return string|null
     */
    public function getCancelUrl()
    {
        return $this->container['cancel_url'];
    }

    /**
     * Sets cancel_url
     *
     * @param string|null $cancel_url The URL the customer will be directed to if they decide to cancel payment and return to your website (used in hosted payment page).
     *
     * @return self
     */
    public function setCancelUrl($cancel_url)
    {
        $this->container['cancel_url'] = $cancel_url;

        return $this;
    }

    /**
     * Gets payment_token
     *
     * @return string|null
     */
    public function getPaymentToken()
    {
        return $this->container['payment_token'];
    }

    /**
     * Sets payment_token
     *
     * @param string|null $payment_token A payment token generated by monei.js [Components](https://docs.monei.com/monei-js/overview/) or a paymentToken [saved after a previous successful payment](https://docs.monei.com/guides/save-payment-method/). In case of the first one, you will also need to send the `sessionId` used to generate the token in the first place.
     *
     * @return self
     */
    public function setPaymentToken($payment_token)
    {
        $this->container['payment_token'] = $payment_token;

        return $this;
    }

    /**
     * Gets session_id
     *
     * @return string|null
     */
    public function getSessionId()
    {
        return $this->container['session_id'];
    }

    /**
     * Sets session_id
     *
     * @param string|null $session_id A unique identifier within your system that adds security to the payment process. You need to pass the same session ID as the one used on the frontend to initialize MONEI Component (if you needed to). This is required if a payment token (not permanent) was already generated in the frontend.
     *
     * @return self
     */
    public function setSessionId($session_id)
    {
        $this->container['session_id'] = $session_id;

        return $this;
    }

    /**
     * Gets generate_payment_token
     *
     * @return bool|null
     */
    public function getGeneratePaymentToken()
    {
        return $this->container['generate_payment_token'];
    }

    /**
     * Sets generate_payment_token
     *
     * @param bool|null $generate_payment_token If set to true a permanent token that represents a payment method used in the payment will be generated.
     *
     * @return self
     */
    public function setGeneratePaymentToken($generate_payment_token)
    {
        $this->container['generate_payment_token'] = $generate_payment_token;

        return $this;
    }

    /**
     * Gets payment_method
     *
     * @return \Monei\Model\PaymentPaymentMethodInput|null
     */
    public function getPaymentMethod()
    {
        return $this->container['payment_method'];
    }

    /**
     * Sets payment_method
     *
     * @param \Monei\Model\PaymentPaymentMethodInput|null $payment_method payment_method
     *
     * @return self
     */
    public function setPaymentMethod($payment_method)
    {
        $this->container['payment_method'] = $payment_method;

        return $this;
    }

    /**
     * Gets allowed_payment_methods
     *
     * @return \Monei\Model\PaymentPaymentMethods|null
     */
    public function getAllowedPaymentMethods()
    {
        return $this->container['allowed_payment_methods'];
    }

    /**
     * Sets allowed_payment_methods
     *
     * @param \Monei\Model\PaymentPaymentMethods|null $allowed_payment_methods allowed_payment_methods
     *
     * @return self
     */
    public function setAllowedPaymentMethods($allowed_payment_methods)
    {
        $this->container['allowed_payment_methods'] = $allowed_payment_methods;

        return $this;
    }

    /**
     * Gets transaction_type
     *
     * @return \Monei\Model\PaymentTransactionType|null
     */
    public function getTransactionType()
    {
        return $this->container['transaction_type'];
    }

    /**
     * Sets transaction_type
     *
     * @param \Monei\Model\PaymentTransactionType|null $transaction_type transaction_type
     *
     * @return self
     */
    public function setTransactionType($transaction_type)
    {
        $this->container['transaction_type'] = $transaction_type;

        return $this;
    }

    /**
     * Gets sequence
     *
     * @return \Monei\Model\PaymentSequence|null
     */
    public function getSequence()
    {
        return $this->container['sequence'];
    }

    /**
     * Sets sequence
     *
     * @param \Monei\Model\PaymentSequence|null $sequence sequence
     *
     * @return self
     */
    public function setSequence($sequence)
    {
        $this->container['sequence'] = $sequence;

        return $this;
    }

    /**
     * Gets store_id
     *
     * @return string|null
     */
    public function getStoreId()
    {
        return $this->container['store_id'];
    }

    /**
     * Sets store_id
     *
     * @param string|null $store_id A unique identifier of the Store. If specified the payment is attached to this Store.
     *
     * @return self
     */
    public function setStoreId($store_id)
    {
        $this->container['store_id'] = $store_id;

        return $this;
    }

    /**
     * Gets point_of_sale_id
     *
     * @return string|null
     */
    public function getPointOfSaleId()
    {
        return $this->container['point_of_sale_id'];
    }

    /**
     * Sets point_of_sale_id
     *
     * @param string|null $point_of_sale_id A unique identifier of the Point of Sale. If specified the payment is attached to this Point of Sale. If there is a QR code attached to the same Point of Sale, this payment will be available by scanning the QR code.
     *
     * @return self
     */
    public function setPointOfSaleId($point_of_sale_id)
    {
        $this->container['point_of_sale_id'] = $point_of_sale_id;

        return $this;
    }

    /**
     * Gets subscription_id
     *
     * @return string|null
     */
    public function getSubscriptionId()
    {
        return $this->container['subscription_id'];
    }

    /**
     * Sets subscription_id
     *
     * @param string|null $subscription_id A unique identifier of the Subscription. If specified the payment is attached to this Subscription.
     *
     * @return self
     */
    public function setSubscriptionId($subscription_id)
    {
        $this->container['subscription_id'] = $subscription_id;

        return $this;
    }

    /**
     * Gets auto_recover
     *
     * @return bool|null
     */
    public function getAutoRecover()
    {
        return $this->container['auto_recover'];
    }

    /**
     * Sets auto_recover
     *
     * @param bool|null $auto_recover If set to `true`, the new payment will be automatically created when customer visits the payment link of the previously failed payment. Is automatically set to `true` if `completeUrl` is not provided.(set this value to `true` to create \"Pay By Link\" payments).
     *
     * @return self
     */
    public function setAutoRecover($auto_recover)
    {
        $this->container['auto_recover'] = $auto_recover;

        return $this;
    }

    /**
     * Gets description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->container['description'];
    }

    /**
     * Sets description
     *
     * @param string|null $description An arbitrary string attached to the payment. Often useful for displaying to users.
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->container['description'] = $description;

        return $this;
    }

    /**
     * Gets customer
     *
     * @return \Monei\Model\PaymentCustomer|null
     */
    public function getCustomer()
    {
        return $this->container['customer'];
    }

    /**
     * Sets customer
     *
     * @param \Monei\Model\PaymentCustomer|null $customer customer
     *
     * @return self
     */
    public function setCustomer($customer)
    {
        $this->container['customer'] = $customer;

        return $this;
    }

    /**
     * Gets billing_details
     *
     * @return \Monei\Model\PaymentBillingDetails|null
     */
    public function getBillingDetails()
    {
        return $this->container['billing_details'];
    }

    /**
     * Sets billing_details
     *
     * @param \Monei\Model\PaymentBillingDetails|null $billing_details billing_details
     *
     * @return self
     */
    public function setBillingDetails($billing_details)
    {
        $this->container['billing_details'] = $billing_details;

        return $this;
    }

    /**
     * Gets shipping_details
     *
     * @return \Monei\Model\PaymentShippingDetails|null
     */
    public function getShippingDetails()
    {
        return $this->container['shipping_details'];
    }

    /**
     * Sets shipping_details
     *
     * @param \Monei\Model\PaymentShippingDetails|null $shipping_details shipping_details
     *
     * @return self
     */
    public function setShippingDetails($shipping_details)
    {
        $this->container['shipping_details'] = $shipping_details;

        return $this;
    }

    /**
     * Gets session_details
     *
     * @return \Monei\Model\PaymentSessionDetails|null
     */
    public function getSessionDetails()
    {
        return $this->container['session_details'];
    }

    /**
     * Sets session_details
     *
     * @param \Monei\Model\PaymentSessionDetails|null $session_details session_details
     *
     * @return self
     */
    public function setSessionDetails($session_details)
    {
        $this->container['session_details'] = $session_details;

        return $this;
    }

    /**
     * Gets expire_at
     *
     * @return float|null
     */
    public function getExpireAt()
    {
        return $this->container['expire_at'];
    }

    /**
     * Sets expire_at
     *
     * @param float|null $expire_at Payment expiration time.
     *
     * @return self
     */
    public function setExpireAt($expire_at)
    {
        $this->container['expire_at'] = $expire_at;

        return $this;
    }

    /**
     * Gets metadata
     *
     * @return object|null
     */
    public function getMetadata()
    {
        return $this->container['metadata'];
    }

    /**
     * Sets metadata
     *
     * @param object|null $metadata A set of key-value pairs that you can attach to a resource. This can be useful for storing additional information about the resource in a structured format.
     *
     * @return self
     */
    public function setMetadata($metadata)
    {
        $this->container['metadata'] = $metadata;

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


