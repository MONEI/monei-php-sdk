<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

return [
    'prefix' => 'Monei\\Internal',

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
        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->notName('/LICENSE|.*\\.md|.*\\.dist|Makefile|composer\\.json|composer\\.lock/')
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
            ->in('vendor/ralouphie')
            ->name('*.php'),
    ],

    'patchers' => [
        function (string $filePath, string $prefix, string $content): string {
            // Don't prefix our own namespace
            if (str_starts_with($filePath, 'lib/')) {
                return $content;
            }
            return $content;
        },
    ],
];