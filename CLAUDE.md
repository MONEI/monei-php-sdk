# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### Development Commands
```bash
# Install dependencies
composer install

# Run tests
composer test

# Run tests with coverage
composer test-coverage

# Check code style (PSR-12)
composer cs-check

# Fix code style issues
composer cs-fix

# Run a single test file
./vendor/bin/phpunit test/Api/PaymentsApiTest.php

# Run a specific test method
./vendor/bin/phpunit --filter testCreatePayment test/Api/PaymentsApiTest.php
```

### SDK Generation Commands (requires Node.js)
```bash
# Install build dependencies
yarn install

# Generate SDK from remote OpenAPI spec
yarn build

# Generate SDK from local OpenAPI spec
yarn build:local

# Create a new release
yarn release
```

## Architecture

This is an OpenAPI-driven PHP SDK for the MONEI payment platform. The codebase follows a clear separation between auto-generated and custom code.

### Key Components

1. **MoneiClient** (`lib/MoneiClient.php`): Main entry point, provides access to all API resources via properties:
   - `$monei->payments` - Payment operations
   - `$monei->subscriptions` - Subscription management
   - `$monei->paymentMethods` - Payment method operations
   - `$monei->bizum` - Bizum-specific endpoints
   - `$monei->applePay` - Apple Pay domain registration

2. **Auto-generated Code** (DO NOT EDIT):
   - `lib/Api/*` - API endpoint classes
   - `lib/Model/*` - Data model classes
   - Generated from OpenAPI spec at https://js.monei.com/api/v1/openapi.json

3. **Custom Enhancements**:
   - MoneiClient constructor and helper methods
   - Webhook signature verification
   - Account ID support for partners
   - Custom User-Agent handling

### Important Patterns

1. **Error Handling**: All API errors throw `ApiException` with detailed error information including response headers and body.

2. **Authentication**: API key passed to MoneiClient constructor. Keys are environment-specific:
   - Test keys: `pk_test_*`
   - Live keys: `pk_live_*`

3. **Partner Integration**: When using Account ID for multi-merchant support:
   ```php
   $monei->setUserAgent('MONEI/YourPlatform/1.0.0'); // Required first
   $monei->setAccountId('MERCHANT_ACCOUNT_ID');
   ```

4. **Webhook Security**: Always verify signatures using raw request body:
   ```php
   $payload = $monei->verifySignature($rawBody, $signature);
   ```

### Testing Approach

- Unit tests for all API classes and models in `test/`
- Mock HTTP responses for isolated testing
- Test data fixtures in test files
- Coverage reports via PHPUnit

### Code Generation Workflow

1. OpenAPI spec defines the API contract
2. `yarn build` generates base SDK code
3. Post-build scripts apply formatting and run tests
4. Custom code preserved in MoneiClient.php

When regenerating the SDK, custom modifications to auto-generated files will be lost. Always implement custom logic in MoneiClient.php or separate files.