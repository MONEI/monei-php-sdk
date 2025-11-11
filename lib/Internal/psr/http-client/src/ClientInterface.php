<?php

namespace Monei\Internal\Psr\Http\Client;

use Monei\Internal\Psr\Http\Message\RequestInterface;
use Monei\Internal\Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface;
}
