#!/bin/bash

set -e

echo "Scoping Guzzle dependencies to avoid conflicts..."

# Ensure vendor directory exists
if [ ! -d "vendor" ]; then
    echo "Installing dependencies first..."
    composer install --no-dev --optimize-autoloader
fi

# Run PHP-Scoper
echo "Running PHP-Scoper..."
./php-scoper.phar add-prefix \
    --output-dir=lib/Internal \
    --config=scoper.inc.php \
    --force

# Update references in SDK files
echo "Updating SDK files to use scoped dependencies..."

# Find all PHP files in lib/ (excluding Internal directory)
find lib -name "*.php" -not -path "lib/Internal/*" | while read -r file; do
    # Create a temporary file
    temp_file="${file}.tmp"
    
    # Replace Guzzle namespaces with scoped versions
    # First check if file already has scoped namespaces
    if grep -q "Monei\\\\Internal\\\\GuzzleHttp" "$file"; then
        # File already scoped, just clean up any double namespacing
        sed -e 's/Monei\\Internal\\Monei\\Internal\\/Monei\\Internal\\/g' \
            -e 's/\\\\Monei\\Internal\\/\\Monei\\Internal\\/g' \
            "$file" > "$temp_file"
    else
        # Apply scoping
        sed -e 's/use GuzzleHttp\\/use Monei\\Internal\\GuzzleHttp\\/g' \
            -e 's/new GuzzleHttp\\/new \\Monei\\Internal\\GuzzleHttp\\/g' \
            -e 's/GuzzleHttp\\Client/\\Monei\\Internal\\GuzzleHttp\\Client/g' \
            -e 's/GuzzleHttp\\ClientInterface/\\Monei\\Internal\\GuzzleHttp\\ClientInterface/g' \
            -e 's/GuzzleHttp\\Exception\\/\\Monei\\Internal\\GuzzleHttp\\Exception\\/g' \
            -e 's/GuzzleHttp\\HandlerStack/\\Monei\\Internal\\GuzzleHttp\\HandlerStack/g' \
            -e 's/GuzzleHttp\\Middleware/\\Monei\\Internal\\GuzzleHttp\\Middleware/g' \
            -e 's/GuzzleHttp\\Psr7\\/\\Monei\\Internal\\GuzzleHttp\\Psr7\\/g' \
            -e 's/GuzzleHttp\\RequestOptions/\\Monei\\Internal\\GuzzleHttp\\RequestOptions/g' \
            -e 's/GuzzleHttp\\Utils/\\Monei\\Internal\\GuzzleHttp\\Utils/g' \
            -e 's/use Psr\\Http\\Message\\/use Monei\\Internal\\Psr\\Http\\Message\\/g' \
            -e 's/use Psr\\Http\\Client\\/use Monei\\Internal\\Psr\\Http\\Client\\/g' \
            -e 's/Psr\\Http\\Message\\RequestInterface/\\Monei\\Internal\\Psr\\Http\\Message\\RequestInterface/g' \
            -e 's/Psr\\Http\\Message\\ResponseInterface/\\Monei\\Internal\\Psr\\Http\\Message\\ResponseInterface/g' \
            "$file" > "$temp_file"
    fi
    
    # Move temp file back
    mv "$temp_file" "$file"
done

# Clean up double backslashes
find lib -name "*.php" -not -path "lib/Internal/*" | while read -r file; do
    sed -i '' 's/\\\\Monei\\Internal\\/\\Monei\\Internal\\/g' "$file"
done

echo "✅ Dependencies scoped successfully!"
echo ""
echo "The SDK now uses internally scoped versions of Guzzle and PSR packages."
echo "This prevents any conflicts with other versions of Guzzle in the environment."