<?php

namespace Monei\Internal;

// Don't redefine the functions if included multiple times.
if (!\function_exists('Monei\Internal\GuzzleHttp\uri_template')) {
    require __DIR__ . '/functions.php';
}
