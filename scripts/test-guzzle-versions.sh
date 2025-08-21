#!/bin/bash

# Test MONEI SDK compatibility with different Guzzle versions
# Usage: ./test-guzzle-versions.sh

set -e

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
SDK_DIR="$(dirname "$SCRIPT_DIR")"
TEMP_DIR="/tmp/monei-guzzle-test-$$"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Testing MONEI SDK compatibility with different Guzzle versions...${NC}"
echo "SDK directory: $SDK_DIR"
echo ""

# Function to test with specific Guzzle version
test_guzzle_version() {
    local version=$1
    local php_version=${2:-php}
    
    echo -e "${YELLOW}Testing with Guzzle $version...${NC}"
    
    # Create temporary directory
    rm -rf "$TEMP_DIR"
    mkdir -p "$TEMP_DIR"
    cd "$TEMP_DIR"
    
    # Create composer.json
    cat > composer.json << EOF
{
    "name": "test/guzzle-compat",
    "require": {
        "php": ">=7.1",
        "guzzlehttp/guzzle": "$version",
        "monei/monei-php-sdk": "*"
    },
    "repositories": [
        {
            "type": "path",
            "url": "$SDK_DIR",
            "options": {
                "symlink": false
            }
        }
    ],
    "minimum-stability": "dev",
    "prefer-stable": true
}
EOF
    
    # Install dependencies
    echo "Installing dependencies..."
    composer install --no-interaction --quiet --prefer-dist 2>/dev/null || {
        echo -e "${RED}Failed to install dependencies with Guzzle $version${NC}"
        return 1
    }
    
    # Copy test script
    cp "$SCRIPT_DIR/test-guzzle-coexistence.php" test.php
    
    # Run test
    if $php_version test.php; then
        echo -e "${GREEN}✅ Test passed with Guzzle $version${NC}"
        echo ""
        return 0
    else
        echo -e "${RED}❌ Test failed with Guzzle $version${NC}"
        echo ""
        return 1
    fi
}

# Function to check PHP version
check_php_version() {
    local required=$1
    local current=$(php -r 'echo PHP_VERSION;')
    
    if [ "$(printf '%s\n' "$required" "$current" | sort -V | head -n1)" = "$required" ]; then
        return 0
    else
        return 1
    fi
}

# Test results
PASSED=0
FAILED=0
SKIPPED=0

# Test with Guzzle 7 (requires PHP 7.2.5+)
if check_php_version "7.2.5"; then
    if test_guzzle_version "^7.0"; then
        ((PASSED++))
    else
        ((FAILED++))
    fi
else
    echo -e "${YELLOW}Skipping Guzzle 7 test (requires PHP 7.2.5+)${NC}"
    ((SKIPPED++))
fi

# Test with Guzzle 6 (requires PHP 5.5+)
if test_guzzle_version "^6.5"; then
    ((PASSED++))
else
    ((FAILED++))
fi

# Test with Guzzle 5 (requires PHP 5.4+, but not PHP 8+)
if check_php_version "8.0.0"; then
    echo -e "${YELLOW}Skipping Guzzle 5 test (not compatible with PHP 8+)${NC}"
    ((SKIPPED++))
else
    if test_guzzle_version "^5.3"; then
        ((PASSED++))
    else
        ((FAILED++))
    fi
fi

# Clean up
rm -rf "$TEMP_DIR"

# Summary
echo "========================================"
echo -e "${GREEN}Passed: $PASSED${NC}"
if [ $FAILED -gt 0 ]; then
    echo -e "${RED}Failed: $FAILED${NC}"
fi
if [ $SKIPPED -gt 0 ]; then
    echo -e "${YELLOW}Skipped: $SKIPPED${NC}"
fi
echo "========================================"

if [ $FAILED -gt 0 ]; then
    exit 1
else
    echo -e "${GREEN}All compatibility tests passed!${NC}"
    exit 0
fi