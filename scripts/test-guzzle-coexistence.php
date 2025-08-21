<?php

/**
 * Test that MONEI SDK can coexist with different versions of Guzzle
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Monei\MoneiClient;
use GuzzleHttp\Client as GlobalGuzzle;

echo "Testing MONEI SDK with globally installed Guzzle...\n";

// Get the globally installed Guzzle version
$globalGuzzleVersion = GlobalGuzzle::VERSION ?? GlobalGuzzle::MAJOR_VERSION ?? 'unknown';
echo "Global Guzzle version: " . $globalGuzzleVersion . "\n";

// Create MONEI client (uses scoped Guzzle internally)
$monei = new MoneiClient('test_api_key');

// Verify MONEI client is using scoped Guzzle
$reflection = new ReflectionClass($monei);
$httpClientProperty = $reflection->getProperty('httpClient');
$httpClientProperty->setAccessible(true);
$httpClient = $httpClientProperty->getValue($monei);

// Check the class name of the HTTP client
$httpClientClass = get_class($httpClient);
echo "MONEI HTTP client class: " . $httpClientClass . "\n";

// Verify it's the scoped version
if (strpos($httpClientClass, 'Monei\Internal\GuzzleHttp') !== false) {
    echo "✅ MONEI SDK is using scoped Guzzle (no conflict)\n";
} else {
    echo "❌ MONEI SDK is not using scoped Guzzle properly\n";
    exit(1);
}

// Try to use both at the same time
try {
    // Use global Guzzle
    $globalClient = new GlobalGuzzle();
    echo "✅ Global Guzzle client created successfully\n";

    // Use MONEI client
    $config = $monei->getConfig();
    echo "✅ MONEI client working correctly\n";

    echo "\n✅ Both Guzzle versions can coexist without conflicts!\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}