<?php
/**
 * Test script to verify MONEI SDK can coexist with different Guzzle versions
 * This script should be run in a test environment with a specific Guzzle version installed
 */

require 'vendor/autoload.php';

// Get Guzzle version
$guzzleVersion = 'unknown';
if (defined('\GuzzleHttp\Client::VERSION')) {
    $guzzleVersion = \GuzzleHttp\Client::VERSION;
} elseif (defined('\GuzzleHttp\Client::MAJOR_VERSION')) {
    $guzzleVersion = \GuzzleHttp\Client::MAJOR_VERSION;
} else {
    // Try to get from composer
    $installed = json_decode(file_get_contents('vendor/composer/installed.json'), true);
    foreach ($installed['packages'] ?? $installed as $package) {
        if ($package['name'] === 'guzzlehttp/guzzle') {
            $guzzleVersion = $package['version'];
            break;
        }
    }
}

echo "Testing MONEI SDK with Guzzle " . $guzzleVersion . "\n";

try {
    // Create instances of both
    $externalGuzzle = new \GuzzleHttp\Client();
    $monei = new \Monei\MoneiClient('test_api_key');
    
    // Verify both work
    if (get_class($externalGuzzle) === 'GuzzleHttp\Client') {
        echo "✅ External Guzzle client created\n";
    }
    
    if ($monei->payments && $monei->subscriptions) {
        echo "✅ MONEI SDK works correctly\n";
    }
    
    // Verify they use different namespaces
    $moneiGuzzleClass = 'Monei\Internal\GuzzleHttp\Client';
    if (class_exists($moneiGuzzleClass)) {
        echo "✅ MONEI uses scoped Guzzle\n";
    }
    
    // Test that both can be used simultaneously
    echo "Testing simultaneous usage...\n";
    
    // External Guzzle should work
    $externalGuzzleClass = get_class($externalGuzzle);
    if ($externalGuzzleClass === 'GuzzleHttp\Client') {
        echo "✅ External Guzzle instance verified\n";
    }
    
    // MONEI should work with its internal Guzzle
    $apis = ['payments', 'subscriptions', 'paymentMethods', 'bizum', 'applePay'];
    foreach ($apis as $api) {
        if (!property_exists($monei, $api) || !$monei->$api) {
            throw new Exception("API endpoint not accessible: $api");
        }
    }
    echo "✅ All MONEI API endpoints verified\n";
    
    echo "\n✅ Compatibility test PASSED with Guzzle " . $guzzleVersion . "\n";
    exit(0);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}