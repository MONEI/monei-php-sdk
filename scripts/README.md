# MONEI SDK Scripts

This directory contains scripts for building and testing the MONEI PHP SDK.

## Build Scripts

### scope-dependencies.sh
Scopes Guzzle and PSR dependencies to prevent conflicts with other versions installed in the environment. This script is automatically run during the build process.

```bash
./scripts/scope-dependencies.sh
```

## Test Scripts

### test-guzzle-compatibility.php
Tests that the SDK uses scoped Guzzle dependencies and doesn't conflict with globally installed Guzzle.

```bash
php scripts/test-guzzle-compatibility.php
```

### test-guzzle-coexistence.php
Tests that the SDK can coexist with different Guzzle versions. This script should be run in an environment where a specific version of Guzzle is installed.

```bash
# First install a specific Guzzle version, then:
php scripts/test-guzzle-coexistence.php
```

### test-guzzle-versions.sh
Comprehensive test that verifies SDK compatibility with Guzzle 5, 6, and 7.

```bash
./scripts/test-guzzle-versions.sh
```

## GitHub Actions Integration

These scripts are automatically run in GitHub Actions:
- On every pull request and push to main/master branches
- Tests against multiple PHP versions (7.4, 8.0, 8.1, 8.2, 8.3, 8.4)
- Tests coexistence with Guzzle 5.x, 6.x, and 7.x

## Why Scoped Dependencies?

The SDK uses PHP-Scoper to bundle Guzzle and PSR dependencies under the `Monei\Internal` namespace. This ensures:
- No conflicts with any version of Guzzle installed in the host environment
- Compatibility with platforms like PrestaShop 1.7 (which uses Guzzle 5)
- Predictable behavior regardless of the host's dependencies