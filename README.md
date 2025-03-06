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

2. **Redirect the customer to the payment page**

After creating a payment, you'll receive a response with a `nextAction.redirectUrl`. Redirect your customer to this URL to show them the MONEI Hosted payment page.

3. **Customer completes the payment**

The customer enters their payment information and completes any required verification steps (like 3D Secure).

4. **Customer is redirected back to your website**

- If the customer completes the payment, they are redirected to the `successUrl` with a `payment_id` query parameter
- If the customer cancels, they are redirected to the `failureUrl`

5. **Receive asynchronous notification**

MONEI sends an HTTP POST request to your `callbackUrl` with the payment result. This ensures you receive the payment status even if the customer closes their browser during the redirect.

For more information about the hosted payment page, visit the [MONEI Hosted Payment Page documentation](https://docs.monei.com/docs/integrations/use-prebuilt-payment-page).

## Webhooks

Webhooks can be configured in the [MONEI Dashboard → Settings → Webhooks](https://dashboard.monei.com/settings/webhooks).

### Signature Verification

When receiving webhooks from MONEI, you should verify the signature to ensure the request is authentic:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

use OpenAPI\Client\Model\PaymentStatus;

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
} catch (\OpenAPI\Client\ApiException $e) {
    // Invalid signature
    http_response_code(401);
    echo json_encode(['error' => 'Invalid signature']);
}
?>
```

### Handling Payment Callbacks

MONEI sends an HTTP POST request to your `callbackUrl` with the payment result. This ensures you receive the payment status even if the customer closes their browser during the redirect.

Example of handling the callback in a PHP script:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

use OpenAPI\Client\Model\PaymentStatus;

$monei = new Monei\MoneiClient('YOUR_API_KEY');

// Parse raw body for signature verification
$rawBody = file_get_contents('php://input');
$signature = $_SERVER['HTTP_MONEI_SIGNATURE'] ?? '';

try {
    // Verify the signature
    $payload = $monei->verifySignature($rawBody, $signature);
    $payment = $payload->data;
    
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
} catch (\OpenAPI\Client\ApiException $e) {
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

For more information about webhooks, visit the [MONEI Webhooks documentation](https://docs.monei.com/docs/webhooks).

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

// Initialize with your partner API key
$monei = new Monei\MoneiClient('YOUR_PARTNER_API_KEY');

// Set a custom User-Agent for your platform (required before setting Account ID)
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