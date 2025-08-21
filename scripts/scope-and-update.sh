#!/bin/bash

set -e

echo "Processing dependencies with PHP-Scoper..."

# 1. First, scope vendor dependencies and SDK files
rm -rf .build lib/Internal
./php-scoper.phar add-prefix \
    --output-dir=.build \
    --config=scoper.inc.php \
    --force

# 2. Move scoped vendor files to lib/Internal
if [ -d ".build/vendor" ]; then
    mkdir -p lib/Internal
    cp -r .build/vendor/* lib/Internal/
fi

# 3. Copy back the updated SDK files (PHP-Scoper updated their imports)
if [ -d ".build/lib" ]; then
    # Copy all PHP files from .build/lib to lib (excluding Internal directory)
    find .build/lib -name "*.php" -not -path "*Internal*" | while read file; do
        # Get relative path from .build/lib/
        rel_path="${file#.build/lib/}"
        # Copy to lib/ preserving directory structure
        mkdir -p "lib/$(dirname "$rel_path")"
        cp "$file" "lib/$rel_path"
    done
fi

# 4. Clean up
rm -rf .build

echo "✅ Dependencies scoped and SDK files updated"