<?php

declare(strict_types=1);

namespace Coolblue\Tests\Http\Client;

use Coolblue\Http\Client\MiddlewareClient;
use Coolblue\Http\Client\MiddlewareInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophet;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class MiddlewareClientTest extends TestCase
{
    public function testHandlingOfMiddlewareInCorrectOrder(): void
    {
        $order = [];

        $prophet = new Prophet();

        $request = $prophet->prophesize(RequestInterface::class);
        $response = $prophet->prophesize(ResponseInterface::class);

        $middlewareOne = $prophet->prophesize(MiddlewareInterface::class);
        $middlewareOne->process(Argument::type(RequestInterface::class), Argument::type(ClientInterface::class))
            ->shouldBeCalled()
            ->will(function (array $arguments) use (&$order) {
                [$request, $client] = $arguments;
                $order[] = 'before one';
                $response = $client->sendRequest($request);
                $order[] = 'after one';
                return $response;
            });

        $middlewareTwo = $prophet->prophesize(MiddlewareInterface::class);
        $middlewareTwo->process(Argument::type(RequestInterface::class), Argument::type(ClientInterface::class))
            ->shouldBeCalled()
            ->will(function (array $arguments) use (&$order) {
                [$request, $client] = $arguments;
                $order[] = 'before two';
                $response = $client->sendRequest($request);
                $order[] = 'after two';

                return $response;
            });

        $client = $prophet->prophesize(ClientInterface::class);
        $client->sendRequest(Argument::type(RequestInterface::class))
            ->shouldBeCalledOnce()
            ->will(function () use (&$order, $response) {
                $order[] = 'client';
                return $response->reveal();
            });

        $middlewareClient = new MiddlewareClient(
            $client->reveal(),
            $middlewareOne->reveal(),
            $middlewareTwo->reveal()
        );

        $response = $middlewareClient->sendRequest($request->reveal());

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals( // json_encode is used to also test the order of elements in the array
            json_encode(['before one', 'before two', 'client', 'after two', 'after one']),
            json_encode($order)
        );
    }
}
