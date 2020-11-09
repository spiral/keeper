<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper;

use Spiral\Keeper\Helper\RouteBuilder;
use Spiral\Router\RouterInterface;

class RouterTest extends TestCase
{
    use HttpTrait;

    protected const NAMESPACE = 'annotation';

    /**
     * @dataProvider routeUris
     * @param string $uri
     * @param string $expected
     */
    public function testRouteUris(string $uri, string $expected): void
    {
        $this->assertSame($expected, $this->get($uri)->getBody()->__toString());
    }

    public function routeUris(): iterable
    {
        return [
            ['/annotation_/without', 'without name'],
            ['/annotation_with', 'with name'],
        ];
    }

    /**
     * @dataProvider routeNames
     * @param string $route
     * @param string $expected
     */
    public function testRouteName(string $route, string $expected): void
    {
        /** @var RouterInterface $router */
        $router = $this->app->get(RouterInterface::class);
        $uri = $router->uri(RouteBuilder::routeName(static::NAMESPACE, $route));

        $this->assertSame($expected, $this->get($uri)->getBody()->__toString());
    }

    public function routeNames(): iterable
    {
        return [
            ['names.withoutName', 'without name'],
            ['with:name', 'with name'],
        ];
    }
}
