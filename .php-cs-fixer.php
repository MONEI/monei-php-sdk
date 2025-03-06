<?php

/**
 * PHP CS Fixer configuration for MONEI PHP SDK
 * @link https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/HEAD/doc/config.rst
 */
$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude([
        'vendor',
        'node_modules',
        '.yarn',
        '.git',
        '.github',
        '.openapi-generator',
    ])
    ->notPath([
        'test.php',
        '.php-cs-fixer.cache',
        '.phpunit.result.cache',
    ]);

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    'phpdoc_order' => true,
    'array_syntax' => ['syntax' => 'short'],
    'strict_comparison' => true,
    'strict_param' => true,
    'no_trailing_whitespace' => false,
    'no_trailing_whitespace_in_comment' => false,
    'braces' => false,
    'single_blank_line_at_eof' => false,
    'blank_line_after_namespace' => false,
    'no_leading_import_slash' => false,
    'phpdoc_summary' => false,
    'comment_to_phpdoc' => false,
])
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setRiskyAllowed(true)
;
