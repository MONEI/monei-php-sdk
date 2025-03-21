# MONEI PHP SDK

[![Latest Stable Version](https://img.shields.io/packagist/v/monei/monei-php-sdk.svg)](https://packagist.org/packages/monei/monei-php-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/monei/monei-php-sdk.svg)](https://packagist.org/packages/monei/monei-php-sdk)
[![License](https://img.shields.io/packagist/l/monei/monei-php-sdk.svg)](https://packagist.org/packages/monei/monei-php-sdk)
[![PHP Version](https://img.shields.io/packagist/php-v/monei/monei-php-sdk.svg)](https://packagist.org/packages/monei/monei-php-sdk)

The MONEI PHP SDK provides convenient access to the [MONEI](https://monei.com/) API from applications written in server-side PHP.

For collecting customer and payment information in the browser, use [monei.js](https://docs.monei.com/docs/monei-js-overview).

## Table of Contents

- [MONEI PHP SDK](#monei-php-sdk)
  - [Table of Contents](#table-of-contents)
  - [Requirements](#requirements)
  - [Installation](#installation)
  - [Basic Usage](#basic-usage)
    - [API Keys](#api-keys)
      - [Types of API Keys](#types-of-api-keys)
      - [API Key Security](#api-key-security)
    - [Test Mode](#test-mode)
    - [Basic Client Usage](#basic-client-usage)
  - [Payment Operations](#payment-operations)
    - [Creating a Payment](#creating-a-payment)
    - [Retrieving a Payment](#retrieving-a-payment)
    - [Refunding a Payment](#refunding-a-payment)
  - [Integration Methods](#integration-methods)
    - [Using the Prebuilt Payment Page](#using-the-prebuilt-payment-page)
      - [Features](#features)
      - [Integration Flow](#integration-flow)
  - [Webhooks](#webhooks)
    - [Signature Verification](#signature-verification)
    - [Handling Payment Callbacks](#handling-payment-callbacks)
      - [Important Notes About Webhooks](#important-notes-about-webhooks)
  - [MONEI Connect for Partners](#monei-connect-for-partners)
    - [Account ID](#account-id)
      - [Setting Account ID after initialization](#setting-account-id-after-initialization)
    - [Custom User-Agent](#custom-user-agent)
      - [Examples with Proper User-Agent Format](#examples-with-proper-user-agent-format)
    - [Managing Multiple Merchant Accounts](#managing-multiple-merchant-accounts)
  - [Development](#development)
    - [Building the SDK](#building-the-sdk)
  - [Tests](#tests)
  - [Code Style](#code-style)
  - [Documentation](#documentation)

## Requirements

- PHP 7.4 or later

## Installation

Install the package using Composer:

```bash
composer require monei/monei-php-sdk
```

Then run `composer install`

## Basic Usage

### API Keys

The MONEI API uses API keys for authentication. You can obtain and manage your API keys in the [MONEI Dashboard](https://dashboard.monei.com/settings/api).

#### Types of API Keys

MONEI provides two types of API keys:

- **Test API Keys**: Use these for development and testing. Transactions made with test API keys are not processed by real payment providers.
- **Live API Keys**: Use these in production environments. Transactions made with live API keys are processed by real payment providers and will move actual money.

Each API key has a distinct prefix that indicates its environment:

- Test API keys start with `pk_test_` (e.g., `pk_test_12345abcdef`)
- Live API keys start with `pk_live_` (e.g., `pk_live_12345abcdef`)

By checking the prefix of an API key, you can quickly determine which environment you're working in. This is especially useful when you're managing multiple projects or environments.

#### API Key Security

Your API keys carry significant privileges, so be sure to keep them secure:

- Keep your API keys confidential and never share them in publicly accessible areas such as GitHub, client-side code, or in your frontend application.
- Use environment variables or a secure vault to store your API keys.
- Restrict API key access to only the IP addresses that need them.
- Regularly rotate your API keys, especially if you suspect they may have been compromised.

```php
// Example of loading API key from environment variable (recommended)
$apiKey = getenv('MONEI_API_KEY');
$monei = new Monei\MoneiClient($apiKey);
```

### Test Mode

To test your integration with MONEI, you need to switch to **test mode** using the toggle in the header of your MONEI Dashboard. When in test mode:

1. Generate your test API key in [MONEI Dashboard → Settings → API Access](https://dashboard.monei.com/settings/api)
2. Configure your payment methods in [MONEI Dashboard → Settings → Payment Methods](https://dashboard.monei.com/settings/payment-methods)

**Important:** Account ID and API key generated in test mode are different from those in live (production) mode and can only be used for testing purposes.

When using test mode, you can simulate various payment scenarios using test card numbers, Bizum phone numbers, and PayPal accounts provided in the [MONEI Testing documentation](https://docs.monei.com/docs/testing/).

### Basic Client Usage

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

use Monei\Model\CreatePaymentRequest;
use Monei\Model\PaymentCustomer;

// Instantiate the client using the API key
$monei = new Monei\MoneiClient('YOUR_API_KEY');

try {
    // Using request classes for better type safety and IDE autocompletion
    $request = new CreatePaymentRequest([
        'amount' => 1250, // 12.50€
        'order_id' => '100100000001',
        'currency' => 'EUR',
        'description' => 'Items description',
        'customer' => new PaymentCustomer([
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

use Monei\Model\CreatePaymentRequest;
use Monei\Model\PaymentCustomer;
use Monei\Model\PaymentBillingDetails;
use Monei\Model\Address;

$monei = new Monei\MoneiClient('YOUR_API_KEY');

try {
    $request = new CreatePaymentRequest([
        'amount' => 1999, // Amount in cents (19.99)
        'order_id' => '12345',
        'currency' => 'EUR',
        'description' => 'Order #12345',
        'customer' => new PaymentCustomer([
            'email' => 'customer@example.com',
            'name' => 'John Doe',
            'phone' => '+34600000000'
        ]),
        'billing_details' => new PaymentBillingDetails([
            'address' => new Address([
                'line1' => '123 Main St',
                'city' => 'Barcelona',
                'country' => 'ES',
                'zip' => '08001'
            ])
        ]),
        'complete_url' => 'https://example.com/success',
        'fail_url' => 'https://example.com/failure',
        'callback_url' => 'https://example.com/webhook'
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

use Monei\ApiException;

$monei = new Monei\MoneiClient('YOUR_API_KEY');

try {
    $payment = $monei->payments->getPayment('pay_123456789');
    echo "Payment status: " . $payment->getStatus() . PHP_EOL;
} catch (ApiException $e) {
    echo 'Error retrieving payment: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Refunding a Payment

Process a full or partial refund:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

use Monei\Model\RefundPaymentRequest;
use Monei\ApiException;

$monei = new Monei\MoneiClient('YOUR_API_KEY');

try {
    $refundRequest = new RefundPaymentRequest([
        'amount' => 500, // Partial refund of 5.00€
        'refund_reason' => 'Customer request'
    ]);
    
    $result = $monei->payments->refund('pay_123456789', $refundRequest);
    echo "Refund created with ID: " . $result->getId() . PHP_EOL;
} catch (ApiException $e) {
    echo 'Error refunding payment: ', $e->getMessage(), PHP_EOL;
}
?>
```

## Integration Methods

### Using the Prebuilt Payment Page

MONEI Hosted Payment Page is the simplest way to securely collect payments from your customers without building your own payment form.

#### Features

- **Designed to remove friction** — Real-time card validation with built-in error messaging
- **Mobile-ready** — Fully responsive design
- **International** — Supports 13 languages
- **Multiple payment methods** — Supports multiple payment methods including Cards, PayPal, Bizum, GooglePay, Apple Pay & Click to Pay
- **Customization and branding** — Customizable logo, buttons and background color
- **3D Secure** — Supports 3D Secure - SCA verification process
- **Fraud and compliance** — Simplified PCI compliance and SCA-ready

You can customize the appearance in your MONEI Dashboard → Settings → Branding.

#### Integration Flow

1. **Create a payment on your server**

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

use Monei\Model\CreatePaymentRequest;
use Monei\Model\PaymentCustomer;

$monei = new Monei\MoneiClient('YOUR_API_KEY');

try {
    $request = new CreatePaymentRequest([
        'amount' => 110, // Amount in cents (1.10)
        'currency' => 'EUR',
        'order_id' => '14379133960355',
        'description' => 'Test Shop - #14379133960355',
        'customer' => new PaymentCustomer([
            'email' => 'customer@example.com'
        ]),
        'callback_url' => 'https://example.com/checkout/callback', // For asynchronous notifications
        'complete_url' => 'https://example.com/checkout/complete', // Redirect after payment
        'fail_url' => 'https://example.com/checkout/cancel' // Redirect if customer cancels
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

2. **Redirect the customer to the payment page**

After creating a payment, you'll receive a response with a `nextAction.redirectUrl`. Redirect your customer to this URL to show them the MONEI Hosted payment page.

3. **Customer completes the payment**

The customer enters their payment information and completes any required verification steps (like 3D Secure).

4. **Customer is redirected back to your website**

- If the customer completes the payment, they are redirected to the `complete_url` with a `payment_id` query parameter
- If the customer cancels, they are redirected to the `fail_url`

5. **Receive asynchronous notification**

MONEI sends an HTTP POST request to your `callback_url` with the payment result. This ensures you receive the payment status even if the customer closes their browser during the redirect.

For more information about the hosted payment page, visit the [MONEI Hosted Payment Page documentation](https://docs.monei.com/docs/integrations/use-prebuilt-payment-page).

## Webhooks

Webhooks can be configured in the [MONEI Dashboard → Settings → Webhooks](https://dashboard.monei.com/settings/webhooks).

### Signature Verification

When receiving webhooks from MONEI, you should verify the signature to ensure the request is authentic:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

use Monei\Model\PaymentStatus;
use Monei\ApiException;

$monei = new Monei\MoneiClient('YOUR_API_KEY');

// Parse raw body for signature verification
$rawBody = file_get_contents('php://input');

// Get the signature from the headers
$signature = $_SERVER['HTTP_MONEI_SIGNATURE'] ?? '';

try {
    // Verify the signature and get the decoded payload
    $payload = $monei->verifySignature($rawBody, $signature);
    
    // Process the webhook
    $eventType = $payload->type;
    
    // The data field contains the Payment object
    $payment = $payload->object;
    
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
} catch (ApiException $e) {
    // Invalid signature
    http_response_code(401);
    echo json_encode(['error' => 'Invalid signature']);
}
?>
```

### Handling Payment Callbacks

MONEI sends an HTTP POST request to your `callback_url` with the payment result. This ensures you receive the payment status even if the customer closes their browser during the redirect.

Example of handling the callback in a PHP script:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

use Monei\Model\PaymentStatus;
use Monei\ApiException;

$monei = new Monei\MoneiClient('YOUR_API_KEY');

// Parse raw body for signature verification
$rawBody = file_get_contents('php://input');
$signature = $_SERVER['HTTP_MONEI_SIGNATURE'] ?? '';

try {
    // Verify the signature
    $payment = $monei->verifySignature($rawBody, $signature);
    
    // Update your order status based on the payment status
    if ($payment->status === PaymentStatus::SUCCEEDED) {
        // Payment successful - fulfill the order
        // Update your database, send confirmation email, etc.
    } else if ($payment->status === PaymentStatus::FAILED) {
        // Payment failed - notify the customer
        // Log the failure, update your database, etc.
    } else if ($payment->status === PaymentStatus::AUTHORIZED) {
        // Payment is authorized but not yet captured
        // You can capture it later
    } else if ($payment->status === PaymentStatus::CANCELED) {
        // Payment was canceled
    }
    
    // Acknowledge receipt of the webhook
    http_response_code(200);
    echo json_encode(['received' => true]);
} catch (ApiException $e) {
    // Invalid signature
    http_response_code(401);
    echo json_encode(['error' => 'Invalid signature']);
}
?>
```

#### Important Notes About Webhooks

1. Always verify the signature to ensure the webhook is coming from MONEI
2. Use the raw request body for signature verification
3. Return a 2xx status code to acknowledge receipt of the webhook
4. Process webhooks asynchronously for time-consuming operations
5. Implement idempotency to handle duplicate webhook events

## MONEI Connect for Partners

If you're a partner or platform integrating with MONEI, you can act on behalf of your merchants by providing their Account ID. This is part of [MONEI Connect](https://docs.monei.com/docs/monei-connect/), which allows platforms to manage multiple merchant accounts.

**Important:** When using Account ID functionality, you must:

1. Use a partner API key (not a regular merchant API key)
2. Provide a custom User-Agent to identify your platform

For more information about MONEI Connect and becoming a partner, visit the [MONEI Connect documentation](https://docs.monei.com/docs/monei-connect/).

### Account ID

#### Setting Account ID after initialization

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

use Monei\Model\CreatePaymentRequest;

// Initialize with your partner API key
$monei = new Monei\MoneiClient('YOUR_PARTNER_API_KEY');

// Set a custom User-Agent for your platform (required before setting Account ID)
$monei->setUserAgent('MONEI/YourPlatform/1.0.0');

// Set Account ID to act on behalf of a merchant
$monei->setAccountId('MERCHANT_ACCOUNT_ID');

// Make API calls on behalf of the merchant
try {
    $request = new CreatePaymentRequest([
        'amount' => 1250,
        'order_id' => '12345',
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

#### Examples with Proper User-Agent Format

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

use Monei\Model\CreatePaymentRequest;

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
            $request = new CreatePaymentRequest([
                'amount' => 1000,
                'order_id' => 'order-' . $merchantId . '-' . time(),
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

## Development

### Building the SDK

The SDK is built using OpenAPI Generator. To build the SDK from the OpenAPI specification:

```bash
# Install dependencies
yarn install

# Generate SDK from remote OpenAPI spec
yarn build

# Generate SDK from local OpenAPI spec
yarn build:local
```

## Tests

To run the unit tests:

```bash
composer install
./vendor/bin/phpunit
```

## Code Style

This project follows the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard. We use [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) to enforce the coding standards.

To check the code style:

```bash
composer cs-check
```

To automatically fix code style issues:

```bash
composer cs-fix
```

## Documentation

For the full documentation, check our [Documentation portal](https://docs.monei.com/).

For a comprehensive overview of all MONEI features and integration options, visit our [main documentation portal](https://docs.monei.com/). There you can explore guides for:

- Using a prebuilt payment page with MONEI Hosted payment page
- Building a custom checkout with MONEI UI components
- Integrating with multiple e-commerce platforms
- Connecting with business platforms and marketplaces