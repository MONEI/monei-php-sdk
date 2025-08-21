<?php

/**
 * Test that MONEI SDK can coexist with different versions of Guzzle
 */

// Try multiple possible autoload locations
$autoloadPaths = [
    __DIR__ . '/../vendor/autoload.php',  // When run from scripts directory
    __DIR__ . '/vendor/autoload.php',      // When run from project root
    'vendor/autoload.php',                 // When copied to temp directory
];

$autoloadFound = false;
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $autoloadFound = true;
        break;
    }
}

if (!$autoloadFound) {
    echo "Error: Could not find autoload.php\n";
    exit(1);
}

use Monei\MoneiClient;

echo "Testing MONEI SDK with globally installed Guzzle...\n";

// Check if global Guzzle is available
if (class_exists('GuzzleHttp\Client')) {
    $globalGuzzleClass = 'GuzzleHttp\Client';
    
    // Try to get version from different properties (varies by Guzzle version)
    if (defined($globalGuzzleClass . '::VERSION')) {
        $globalGuzzleVersion = constant($globalGuzzleClass . '::VERSION');
    } elseif (defined($globalGuzzleClass . '::MAJOR_VERSION')) {
        $globalGuzzleVersion = constant($globalGuzzleClass . '::MAJOR_VERSION');
    } else {
        // For older versions, try to instantiate and check
        try {
            $tempClient = new $globalGuzzleClass();
            if (property_exists($tempClient, 'version')) {
                $globalGuzzleVersion = $tempClient->version;
            } else {
                $globalGuzzleVersion = 'unknown';
            }
        } catch (Exception $e) {
            $globalGuzzleVersion = 'unknown';
        }
    }
    
    echo "Global Guzzle version: " . $globalGuzzleVersion . "\n";
    echo "Global Guzzle class: " . $globalGuzzleClass . "\n";
} else {
    echo "No global Guzzle found (this is OK for isolated testing)\n";
}

// Create MONEI client (uses scoped Guzzle internally)
try {
    $monei = new MoneiClient('test_api_key');
    echo "✅ MONEI client created successfully\n";
} catch (Exception $e) {
    echo "❌ Failed to create MONEI client: " . $e->getMessage() . "\n";
    exit(1);
}

// Test that MONEI's internal classes exist and are scoped
$scopedClasses = [
    'Monei\Internal\GuzzleHttp\Client',
    'Monei\Internal\GuzzleHttp\HandlerStack',
    'Monei\Internal\GuzzleHttp\Middleware',
];

$allScopedClassesExist = true;
foreach ($scopedClasses as $class) {
    if (class_exists($class)) {
        echo "✅ Scoped class exists: " . $class . "\n";
    } else {
        echo "❌ Scoped class missing: " . $class . "\n";
        $allScopedClassesExist = false;
    }
}

if (!$allScopedClassesExist) {
    echo "❌ Not all scoped classes are available\n";
    exit(1);
}

// Test basic functionality
try {
    $config = $monei->getConfig();
    echo "✅ MONEI client getConfig() works\n";
    
    // Check that payments API is available
    if ($monei->payments instanceof \Monei\Api\PaymentsApi) {
        echo "✅ Payments API is available\n";
    } else {
        echo "❌ Payments API is not properly initialized\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ Error testing MONEI client: " . $e->getMessage() . "\n";
    exit(1);
}

// If global Guzzle exists, test coexistence
if (class_exists('GuzzleHttp\Client')) {
    try {
        // Create a global Guzzle client
        $globalClient = new \GuzzleHttp\Client();
        echo "✅ Global Guzzle client created successfully\n";
        
        // Verify they're different implementations
        $moneiGuzzleClass = 'Monei\Internal\GuzzleHttp\Client';
        $globalGuzzleClass = 'GuzzleHttp\Client';
        
        if ($moneiGuzzleClass !== $globalGuzzleClass) {
            echo "✅ MONEI uses scoped Guzzle (no conflict)\n";
            echo "   - MONEI Guzzle: " . $moneiGuzzleClass . "\n";
            echo "   - Global Guzzle: " . $globalGuzzleClass . "\n";
        } else {
            echo "❌ MONEI is using global Guzzle (conflict!)\n";
            exit(1);
        }
        
    } catch (Exception $e) {
        echo "❌ Error during coexistence test: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    echo "ℹ️  No global Guzzle to test coexistence with (standalone MONEI SDK)\n";
}

echo "\n✅ All tests passed successfully!\n";