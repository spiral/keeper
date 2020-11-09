<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper;

use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait HttpTrait
{
    private function post($uri, array $data = [], array $headers = [], array $cookies = []): ResponseInterface
    {
        return $this->app->getHttp()->handle(
            $this->request($uri, 'POST', [], $headers, $cookies)->withParsedBody($data)
        );
    }

    private function get($uri, array $query = [], array $headers = [], array $cookies = []): ResponseInterface
    {
        return $this->app->getHttp()->handle($this->request($uri, 'GET', $query, $headers, $cookies));
    }

    private function request(
        $uri,
        string $method,
        array $query = [],
        array $headers = [],
        array $cookies = []
    ): ServerRequestInterface {
        $headers = array_merge(['accept-language' => 'en'], $headers);

        return new ServerRequest([], [], $uri, $method, 'php://input', $headers, $cookies, $query);
    }
}
