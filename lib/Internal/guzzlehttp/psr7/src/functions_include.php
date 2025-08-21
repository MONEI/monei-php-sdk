<?php

namespace Monei\Internal;

// Don't redefine the functions if included multiple times.
if (!\function_exists('Monei\Internal\GuzzleHttp\Psr7\str')) {
    require __DIR__ . '/functions.php';
}
