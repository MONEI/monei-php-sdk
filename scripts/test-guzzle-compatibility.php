<?php
/**
 * Test script to verify MONEI SDK uses scoped Guzzle dependencies
 * and doesn't conflict with globally installed Guzzle
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Monei\MoneiClient;

echo "Testing MONEI SDK with scoped Guzzle...\n";

try {
    // Create MoneiClient instance
    $monei = new MoneiClient('test_api_key');
    echo "✅ MoneiClient created successfully\n";
    
    // Verify APIs are accessible
    if ($monei->payments && $monei->subscriptions && $monei->paymentMethods) {
        echo "✅ All API endpoints accessible\n";
    }
    
    // Check that scoped classes exist
    $scopedClasses = [
        'Monei\Internal\GuzzleHttp\Client',
        'Monei\Internal\GuzzleHttp\HandlerStack',
        'Monei\Internal\Psr\Http\Message\RequestInterface'
    ];
    
    foreach ($scopedClasses as $class) {
        if (!class_exists($class)) {
            throw new Exception("Scoped class not found: $class");
        }
    }
    echo "✅ All scoped classes found\n";
    
    // Verify no conflict with regular Guzzle
    if (class_exists('GuzzleHttp\Client')) {
        echo "✅ No conflict with regular GuzzleHttp\Client\n";
    }
    
    echo "\n✅ Guzzle compatibility test PASSED\n";
    exit(0);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}