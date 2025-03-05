# MONEI PHP SDK

The MONEI PHP SDK provides convenient access to the [MONEI](https://monei.com/) API from applications written in server-side PHP.

For collecting customer and payment information in the browser, use [monei.js](https://docs.monei.com/docs/monei-js-overview).

## Table of Contents

- [MONEI PHP SDK](#monei-php-sdk)
  - [Table of Contents](#table-of-contents)
  - [Requirements](#requirements)
  - [Installation](#installation)
  - [Basic Usage](#basic-usage)
  - [Payment Operations](#payment-operations)
    - [Creating a Payment](#creating-a-payment)
    - [Retrieving a Payment](#retrieving-a-payment)
    - [Refunding a Payment](#refunding-a-payment)
  - [Integration Methods](#integration-methods)
    - [Using the Prebuilt Payment Page](#using-the-prebuilt-payment-page)
  - [Webhooks](#webhooks)
    - [Signature Verification](#signature-verification)
    - [Important Notes About Webhooks](#important-notes-about-webhooks)
  - [MONEI Connect for Partners](#monei-connect-for-partners)
    - [Account ID](#account-id)
    - [Custom User-Agent](#custom-user-agent)
    - [Managing Multiple Merchant Accounts](#managing-multiple-merchant-accounts)
  - [Tests](#tests)
  - [Documentation](#documentation)

## Requirements

PHP 7.2 and later

## Installation

Install the package using Composer:

```bash
composer require monei/monei-php-sdk
```

Or add the following to `composer.json`:

```json
{
  "require": {
    "monei/monei-php-sdk": "^1.2.0"
  }
}
```

Then run `composer install`

## Basic Usage

The MONEI API uses API key to authenticate requests. You can view and manage your API key in the [MONEI Dashboard](https://dashboard.monei.com/settings/api).

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Instantiate the client using the API key
$monei = new Monei\MoneiClient('YOUR_API_KEY');

try {
    // Using request classes for better type safety and IDE autocompletion
    $request = new \OpenAPI\Client\Model\CreatePaymentRequest([
        'amount' => 1250, // 12.50€
        'orderId' => '100100000001',
        'currency' => 'EUR',
        'description' => 'Items description',
        'customer' => new \OpenAPI\Client\Model\Customer([
            'email' => 'john.doe@monei.com',
            'name' => 'John Doe'
        ])
    ]);
    
    $result = $monei->payments->create($request);
    print_r($result);
} catch (Exception $e) {
    echo 'Error while creating payment: ', $e->getMessage(), PHP_EOL;
}
?>
```

## Payment Operations

### Creating a Payment

Create a payment with customer information:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$monei = new Monei\MoneiClient('YOUR_API_KEY');

try {
    $request = new \OpenAPI\Client\Model\CreatePaymentRequest([
        'amount' => 1999, // Amount in cents (19.99)
        'orderId' => '12345',
        'currency' => 'EUR',
        'description' => 'Order #12345',
        'customer' => new \OpenAPI\Client\Model\Customer([
            'email' => 'customer@example.com',
            'name' => 'John Doe',
            'phone' => '+34600000000'
        ]),
        'billingDetails' => new \OpenAPI\Client\Model\BillingDetails([
            'address' => new \OpenAPI\Client\Model\Address([
                'line1' => '123 Main St',
                'city' => 'Barcelona',
                'country' => 'ES',
                'postalCode' => '08001'
            ])
        ]),
        'successUrl' => 'https://example.com/success',
        'failureUrl' => 'https://example.com/failure',
        'callbackUrl' => 'https://example.com/webhook'
    ]);
    
    $result = $monei->payments->create($request);
    print_r($result);
} catch (Exception $e) {
    echo 'Error while creating payment: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Retrieving a Payment

Retrieve an existing payment by ID:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$monei = new Monei\MoneiClient('YOUR_API_KEY');

try {
    $payment = $monei->payments->getPayment('pay_123456789');
    echo "Payment status: " . $payment->getStatus() . PHP_EOL;
} catch (\OpenAPI\Client\ApiException $e) {
    echo 'Error retrieving payment: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Refunding a Payment

Process a full or partial refund:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$monei = new Monei\MoneiClient('YOUR_API_KEY');

try {
    $refundRequest = new \OpenAPI\Client\Model\RefundPaymentRequest([
        'amount' => 500, // Partial refund of 5.00€
        'reason' => 'Customer request'
    ]);
    
    $result = $monei->payments->refund('pay_123456789', $refundRequest);
    echo "Refund created with ID: " . $result->getId() . PHP_EOL;
} catch (\OpenAPI\Client\ApiException $e) {
    echo 'Error refunding payment: ', $e->getMessage(), PHP_EOL;
}
?>
```

## Integration Methods

### Using the Prebuilt Payment Page

MONEI Hosted Payment Page is the simplest way to securely collect payments from your customers without building your own payment form.

1. **Create a payment on your server**

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$monei = new Monei\MoneiClient('YOUR_API_KEY');

try {
    $request = new \OpenAPI\Client\Model\CreatePaymentRequest([
        'amount' => 110, // Amount in cents (1.10)
        'currency' => 'EUR',
        'orderId' => '14379133960355',
        'description' => 'Test Shop - #14379133960355',
        'customer' => new \OpenAPI\Client\Model\Customer([
            'email' => 'customer@example.com'
        ]),
        'callbackUrl' => 'https://example.com/checkout/callback', // For asynchronous notifications
        'successUrl' => 'https://example.com/checkout/complete', // Redirect after payment
        'failureUrl' => 'https://example.com/checkout/cancel' // Redirect if customer cancels
    ]);
    
    $result = $monei->payments->create($request);
    
    // Redirect the customer to the payment page
    if (isset($result->getNextAction()) && isset($result->getNextAction()->getRedirectUrl())) {
        header('Location: ' . $result->getNextAction()->getRedirectUrl());
        exit;
    }
} catch (Exception $e) {
    echo 'Error while creating payment: ', $e->getMessage(), PHP_EOL;
}
?>
```

2. **Customer completes the payment**

The customer enters their payment information and completes any required verification steps (like 3D Secure).

3. **Customer is redirected back to your website**

- If the customer completes the payment, they are redirected to the `successUrl` with a `payment_id` query parameter
- If the customer cancels, they are redirected to the `failureUrl`

4. **Receive asynchronous notification**

MONEI sends an HTTP POST request to your `callbackUrl` with the payment result. This ensures you receive the payment status even if the customer closes their browser during the redirect.

For more information about the hosted payment page, visit the [MONEI Hosted Payment Page documentation](https://docs.monei.com/docs/integrations/use-prebuilt-payment-page).

## Webhooks

### Signature Verification

When receiving webhooks from MONEI, you should verify the signature to ensure the request is authentic:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$monei = new Monei\MoneiClient('YOUR_API_KEY');

// Get the raw request body
$rawBody = file_get_contents('php://input');

// Get the signature from the headers
$signature = $_SERVER['HTTP_MONEI_SIGNATURE'] ?? '';

try {
    // Verify the signature and get the decoded payload
    $payload = $monei->verifySignature($rawBody, $signature);
    
    // Process the webhook
    $eventType = $payload->type;
    
    // The data field contains the Payment object
    $payment = $payload->data;
    
    // Access Payment object properties directly
    $paymentId = $payment->id;
    $amount = $payment->amount;
    $currency = $payment->currency;
    $status = $payment->status;
    
    // Handle the event based on its type
    switch ($eventType) {
        case 'payment.succeeded':
            // Handle successful payment
            echo "Payment {$paymentId} succeeded: " . ($amount/100) . " {$currency}\n";
            break;
        case 'payment.failed':
            // Handle failed payment
            echo "Payment {$paymentId} failed with status: {$status}\n";
            break;
        // Handle other event types
    }
    
    http_response_code(200);
    echo json_encode(['received' => true]);
} catch (\OpenAPI\Client\ApiException $e) {
    // Invalid signature
    http_response_code(401);
    echo json_encode(['error' => 'Invalid signature']);
}
?>
```

### Important Notes About Webhooks

1. Always verify the signature to ensure the webhook is coming from MONEI
2. Use the raw request body for signature verification
3. Return a 2xx status code to acknowledge receipt of the webhook
4. Process webhooks asynchronously for time-consuming operations
5. Implement idempotency to handle duplicate webhook events

For more information about webhooks, visit the [MONEI Webhooks documentation](https://docs.monei.com/docs/webhooks).

## MONEI Connect for Partners

If you're a platform or marketplace integrating with MONEI, you can make API calls on behalf of your merchants using MONEI Connect. This feature is specifically for MONEI partners who have a partner API key.

**Important:** When using Account ID functionality, you must:

1. Use a partner API key (not a regular merchant API key)
2. Provide a custom User-Agent to identify your platform

For more information about MONEI Connect and becoming a partner, visit the [MONEI Connect documentation](https://docs.monei.com/docs/monei-connect/).

### Account ID

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Initialize with your partner API key
$monei = new Monei\MoneiClient('YOUR_PARTNER_API_KEY');

// Set a custom User-Agent for your platform (required when using Account ID)
// Format should be: MONEI/<PARTNER_NAME>/<VERSION>
$monei->setUserAgent('MONEI/YourPlatform/1.0.0');

// Set Account ID to act on behalf of a merchant
$monei->setAccountId('MERCHANT_ACCOUNT_ID');

// Make API calls on behalf of the merchant
try {
    $request = new \OpenAPI\Client\Model\CreatePaymentRequest([
        'amount' => 1250,
        'orderId' => '12345',
        'currency' => 'EUR'
    ]);
    
    $result = $monei->payments->create($request);
    print_r($result);
} catch (Exception $e) {
    echo 'Error while creating payment: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Custom User-Agent

When integrating as a MONEI Connect partner, your User-Agent should follow this format:

```
MONEI/<PARTNER_NAME>/<VERSION>
```

For example: `MONEI/YourPlatform/1.0.0`

This format helps MONEI identify your platform in API requests and is required when using the Partner API Key.

```php
<?php
// For a platform named "ShopManager" with version 2.1.0
$monei->setUserAgent('MONEI/ShopManager/2.1.0');

// For a platform named "PaymentHub" with version 3.0.1
$monei->setUserAgent('MONEI/PaymentHub/3.0.1');
?>
```

> **Note:** When using Account ID, you must set a custom User-Agent before making any API calls. The User-Agent is validated when making API requests.
>
> **Important:** To use this feature, you need to be registered as a MONEI partner and use your partner API key. Please contact connect@monei.com to register as a partner.

### Managing Multiple Merchant Accounts

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Initialize with your partner API key
$monei = new Monei\MoneiClient('YOUR_PARTNER_API_KEY');

// Set a custom User-Agent for your platform
$monei->setUserAgent('MONEI/YourPlatform/1.0.0');

// Function to process payments for multiple merchants
function processPaymentsForMerchants($monei, $merchantAccounts) {
    $results = [];

    foreach ($merchantAccounts as $merchantId) {
        // Set the current merchant account
        $monei->setAccountId($merchantId);

        // Process payment for this merchant
        try {
            $request = new \OpenAPI\Client\Model\CreatePaymentRequest([
                'amount' => 1000,
                'orderId' => 'order-' . $merchantId . '-' . time(),
                'currency' => 'EUR'
            ]);
            
            $payment = $monei->payments->create($request);
            $results[$merchantId] = [
                'success' => true,
                'payment' => $payment
            ];
        } catch (Exception $e) {
            $results[$merchantId] = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    return $results;
}

// Example usage
$merchantAccounts = ['merchant_1', 'merchant_2', 'merchant_3'];
$results = processPaymentsForMerchants($monei, $merchantAccounts);
print_r($results);
?>
```

## Tests

To run the unit tests:

```bash
composer install
./vendor/bin/phpunit
```

## Documentation

For the full documentation, check our [Documentation portal](https://docs.monei.com/).

For a comprehensive overview of all MONEI features and integration options, visit our [main documentation portal](https://docs.monei.com/). There you can explore guides for:

- Using a prebuilt payment page with MONEI Hosted payment page
- Building a custom checkout with MONEI UI components
- Integrating with multiple e-commerce platforms
- Connecting with business platforms and marketplaces
