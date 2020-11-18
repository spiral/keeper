<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Route;

use Spiral\Keeper\Helper\RouteBuilder;
use Spiral\Router\Exception\UndefinedRouteException;
use Spiral\Router\RouterInterface;
use Spiral\Tests\Keeper\HttpTrait;
use Spiral\Tests\Keeper\TestCase;

class TestControllerDefaults extends TestCase
{
    use HttpTrait;

    public function testHasConfigDefaultControllerAndDefaultAction(): void
    {
        $response = $this->get('/controllerDefault');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertEquals('controllerDefault: defaults', (string)$response->getBody());
    }

    public function testHasConfigDefaultControllerWithOwnDefaultAction(): void
    {
        $response = $this->get('/controllerDefault2');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertEquals('controllerDefault2: defaults', (string)$response->getBody());
    }

    public function testHasConfigDefaultControllerWithFallbackAction(): void
    {
        $response = $this->get('/controllerDefault3');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertEquals('controllerDefault3: defaults', (string)$response->getBody());
    }
}
