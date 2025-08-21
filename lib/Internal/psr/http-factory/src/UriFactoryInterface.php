<?php

namespace Monei\Internal\Psr\Http\Message;

interface UriFactoryInterface
{
    /**
     * Create a new URI.
     *
     * @param string $uri
     *
     *
     * @throws \InvalidArgumentException If the given URI cannot be parsed.
     * @return UriInterface
     */
    public function createUri(string $uri = ''): UriInterface;
}
