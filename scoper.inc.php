<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

return [
    'prefix' => 'Monei\\Internal',

    // Don't prefix the SDK's own namespace
    'exclude-namespaces' => [
        'Monei',
        'Symfony\Polyfill',
    ],

    'exclude-classes' => [
        'Composer\Autoload\ClassLoader',
    ],

    'exclude-functions' => [],

    'exclude-constants' => [],

    'expose-global-constants' => true,
    'expose-global-classes' => true,
    'expose-global-functions' => true,

    'finders' => [
        // Process vendor dependencies to scope them
        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->notName('/LICENSE|.*\.md|.*\.dist|Makefile|composer\.json|composer\.lock/')
            ->exclude([
                'doc',
                'test',
                'test_old',
                'tests',
                'Tests',
                'vendor-bin',
                'bin',
            ])
            ->in('vendor/guzzlehttp')
            ->in('vendor/psr/http-message')
            ->in('vendor/psr/http-client')
            ->in('vendor/psr/http-factory')
            ->in('vendor/ralouphie')
            ->name('*.php'),

        // ALSO process the SDK files to update their imports
        // Since 'Monei' namespace is excluded, these files won't be prefixed
        // but their imports WILL be updated to use scoped versions
        Finder::create()
            ->files()
            ->in('lib')
            ->notPath('Internal')
            ->name('*.php'),
    ],

    'patchers' => [
        function (string $filePath, string $prefix, string $content): string {
            // Fix Utils::jsonEncode calls to use the function instead
            // After PHP-Scoper has already updated namespaces
            if (str_contains($content, 'Utils::jsonEncode')) {
                $content = str_replace(
                    '\\' . $prefix . '\\GuzzleHttp\\Utils::jsonEncode',
                    '\\' . $prefix . '\\GuzzleHttp\\json_encode',
                    $content
                );
            }

            return $content;
        },
    ],
];