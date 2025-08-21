# Changelog

## 2.7.0 (2025-08-21)

* fix: update MoneiClient constructor to accept nullable Configuration parameter ([4b884e3](https://github.com/MONEI/monei-php-sdk/commit/4b884e3))
* chore: update PHP versions in GitHub Actions workflow to include 8.3 and 8.4 ([f2aebd1](https://github.com/MONEI/monei-php-sdk/commit/f2aebd1))
* refactor: regenerate SDK with OpenAPI Generator 7.14.0 ([33066b9](https://github.com/MONEI/monei-php-sdk/commit/33066b9))

## <small>2.6.18 (2025-07-02)</small>

* chore: clean up whitespace in MoneiClientUserAgentTest ([86246ea](https://github.com/MONEI/monei-php-sdk/commit/86246ea))
* chore: rename namespace in MoneiClientUserAgentTest to follow project structure ([c50ee2c](https://github.com/MONEI/monei-php-sdk/commit/c50ee2c))
* chore: reorder use statements and improve user agent condition in MoneiClient ([26e443f](https://github.com/MONEI/monei-php-sdk/commit/26e443f))
* chore: update MoneiClient to conditionally set default user agent and ensure test isolation ([d439c70](https://github.com/MONEI/monei-php-sdk/commit/d439c70))

## <small>2.6.17 (2025-06-01)</small>

* chore: downgrade guzzlehttp/guzzle version to ^7.0.0 in composer.json ([70c84ad](https://github.com/MONEI/monei-php-sdk/commit/70c84ad))

## <small>2.6.16 (2025-05-29)</small>

* chore: update OpenAPI document version to 1.7.0 across all relevant files ([3af8b62](https://github.com/MONEI/monei-php-sdk/commit/3af8b62))

## <small>2.6.15 (2025-05-28)</small>

* chore: add empty code blocks to setup and teardown methods in SubscriptionsApiTest ([51f7180](https://github.com/MONEI/monei-php-sdk/commit/51f7180))
* chore: update OpenAPI document version to 1.6.4 ([98481b4](https://github.com/MONEI/monei-php-sdk/commit/98481b4))

## <small>2.6.14 (2025-04-29)</small>

* chore: update OpenAPI document version to 1.6.1 ([3b9c845](https://github.com/MONEI/monei-php-sdk/commit/3b9c845))
* Fix. Bizum api initialization ([593e5ba](https://github.com/MONEI/monei-php-sdk/commit/593e5ba))
* Update MoneiClient.php ([d0b3b9f](https://github.com/MONEI/monei-php-sdk/commit/d0b3b9f))

## <small>2.6.13 (2025-03-14)</small>

* chore: implement missing test methods for model classes ([0e46f02](https://github.com/MONEI/monei-php-sdk/commit/0e46f02))
* chore: standardize test model files with empty setup and teardown methods ([868c1e0](https://github.com/MONEI/monei-php-sdk/commit/868c1e0))
* chore: update OpenAPI document version to 1.6.0 ([772fa1b](https://github.com/MONEI/monei-php-sdk/commit/772fa1b))
* chore: update package test files with package name correction ([fccdd49](https://github.com/MONEI/monei-php-sdk/commit/fccdd49))

## <small>2.6.12 (2025-03-11)</small>

* chore: update commitlint configuration to disable body line length limit ([857196e](https://github.com/MONEI/monei-php-sdk/commit/857196e))
* chore: update SDK to OpenAPI document version 1.5.8 ([b07c169](https://github.com/MONEI/monei-php-sdk/commit/b07c169))
* fix: update MONEI webhook signature verification method ([c6c8e21](https://github.com/MONEI/monei-php-sdk/commit/c6c8e21))

## <small>2.6.11 (2025-03-08)</small>

* chore: remove deprecated Apple Pay domain response models and update test files ([c6eaad0](https://github.com/MONEI/monei-php-sdk/commit/c6eaad0))
* docs: remove outdated MONEI webhooks documentation link ([03eac80](https://github.com/MONEI/monei-php-sdk/commit/03eac80))

## <small>2.6.10 (2025-03-07)</small>

* chore: downgrade OpenAPI generator version and update SDK ([6a4b6ff](https://github.com/MONEI/monei-php-sdk/commit/6a4b6ff))
* chore: migrate to ES modules and update commitlint config ([54add7e](https://github.com/MONEI/monei-php-sdk/commit/54add7e))

## <small>2.6.8 (2025-03-07)</small>

* refactor: optimize README imports and code examples ([531e7d7](https://github.com/MONEI/monei-php-sdk/commit/531e7d7))
* chore: add commitlint and conventional changelog configuration ([d37dc0a](https://github.com/MONEI/monei-php-sdk/commit/d37dc0a))

All notable changes to the MONEI PHP SDK will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

This changelog is automatically generated with [release-it](https://github.com/release-it/release-it)
and [@release-it/conventional-changelog](https://github.com/release-it/conventional-changelog).

## [Unreleased]

### Added
- Initial setup with conventional-changelog
