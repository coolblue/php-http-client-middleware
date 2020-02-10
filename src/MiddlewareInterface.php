<?php

declare(strict_types=1);

namespace Coolblue\Http\Client;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface MiddlewareInterface
{
    public function process(RequestInterface $request, ClientInterface $client): ResponseInterface;
}
