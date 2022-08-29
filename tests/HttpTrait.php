<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper;

use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\Router\Exception\RouteNotFoundException;

trait HttpTrait
{
    private function post($uri, array $data = [], array $headers = [], array $cookies = []): ResponseInterface
    {
        try {
            return $this->app->getHttp()->handle(
                $this->request($uri, 'POST', [], $headers, $cookies)->withParsedBody($data)
            );
        } catch (RouteNotFoundException $e) {
            return new NotFoundResponse();
        }
    }

    private function get(
        $uri,
        array $query = [],
        array $headers = [],
        array $cookies = [],
        array $attributes = []
    ): ResponseInterface {
        try {
            $request = $this->request($uri, 'GET', $query, $headers, $cookies);
            foreach ($attributes as $name => $attribute) {
                $request = $request->withAttribute($name, $attribute);
            }
            return $this->app->getHttp()->handle($request);
        } catch (RouteNotFoundException $e) {
            return new NotFoundResponse();
        }
    }

    private function request(
        $uri,
        string $method,
        array $query = [],
        array $headers = [],
        array $cookies = []
    ): ServerRequestInterface {
        $headers = array_merge(['accept-language' => 'en'], $headers);

        $request = new ServerRequest(
            method: $method,
            uri: $uri,
            headers: $headers,
            body: 'php://input'
        );

        $request = $request
            ->withCookieParams($cookies)
            ->withQueryParams($query);

        return $request;
    }
}
