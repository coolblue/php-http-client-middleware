<?php

declare(strict_types=1);

namespace Coolblue\Http\Client;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class MiddlewareClient implements ClientInterface
{
    /** @var ClientInterface */
    private $client;

    /** @var MiddlewareInterface[] */
    private $middleware;

    public function __construct(ClientInterface $client, MiddlewareInterface ...$middleware)
    {
        $this->client = $client;
        $this->middleware = $middleware;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $middleware = reset($this->middleware);
        if ($middleware instanceof MiddlewareInterface) {
            $new = new self($this->client, ...array_slice($this->middleware, 1));
            return $middleware->process($request, $new);
        }

        return $this->client->sendRequest($request);
    }
}
