<?php

namespace MergeInc\Sort\Dependencies\Psr\Http\Client;

use MergeInc\Sort\Dependencies\Psr\Http\Message\RequestInterface;
use MergeInc\Sort\Dependencies\Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \MergeInc\Sort\Dependencies\Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface;
}
