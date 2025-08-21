<?php

namespace Monei\Internal;

// Don't redefine the functions if included multiple times.
if (!\function_exists('Monei\Internal\GuzzleHttp\describe_type')) {
    require __DIR__ . '/functions.php';
}
