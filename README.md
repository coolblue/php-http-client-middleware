# HTTP Client middleware

HTTP Client middleware is a middleware solution for [PSR-18 (HTTP Client)](http://www.php-fig.org/psr/psr-18). 

This package contains both a middleware interface as well as a ready to use HTTP Client that is capable of handling middleware.

**NB** This package does not contain any middleware implementations.

## Rationale
The interface offered by PSR-18 (HTTP Client) does not offer any (configuration) options offered by known HTTP abstractions - for example Guzzle and Symfony HttpClient. This makes implementations of this interface exchangeable, but does require you to write these configurations yourself. This can mean additional code that is repeated in multiple locations. Something that might be undesirable. 

This might be solved by using a solution similar to the middleware defined in [PSR-15 (HTTP Server Request Handlers)](https://www.php-fig.org/psr/psr-15). Using middleware will allow centralization of functionality without the necessity of extending or wrapping a client. It also enables you to perform actions both _before_ performing the actual request and _after_ the actual request. 

## Installation

Coming soon.

## Usage 
Middleware needs to comply to the interface `\Coolblue\Http\Client\MiddlewareInterface`:

```php
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

```

To create a middleware enabled client:

```php

$client = new Client(); // an instance of \Psr\Http\Client\ClientInterface
$middlewareOne = new Middleware(); // an instance of \Coolblue\Client\Http\MiddlewareInterface
$middlewareTwo = new Middleware(); // an instance of \Coolblue\Client\Http\MiddlewareInterface

$middlewareClient = new \Coolblue\Http\Client\MiddlewareClient(
    $client, 
    $middlewareOne, 
    $middlewareTwo
);
```
