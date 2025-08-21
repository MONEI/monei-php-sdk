<?php

namespace Monei\Internal;

// Don't redefine the functions if included multiple times.
if (!\function_exists('Monei\Internal\GuzzleHttp\Promise\promise_for')) {
    require __DIR__ . '/functions.php';
}
