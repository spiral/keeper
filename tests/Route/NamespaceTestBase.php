<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Route;

use Spiral\Keeper\Helper\RouteBuilder;
use Spiral\Tests\Keeper\HttpTrait;
use Spiral\Tests\Keeper\TestCase;

abstract class NamespaceTestBase extends TestCase
{
    use HttpTrait;

    protected const NAMESPACE = '';
    protected const PREFIX    = '';

    /**
     * @dataProvider routeUris
     * @param string $uri
     * @param string $expected
     */
    public function testRouteUris(string $uri, string $expected): void
    {
        $this->assertSame($expected, $this->get(static::PREFIX . $uri)->getBody()->__toString());
    }

    public function routeUris(): iterable
    {
        return [
            ['/without', 'name: without name'],
            ['with', 'name: with name'],
            ['/pref/ix_/without', 'prefix: without name'],
            ['/pref/ix_with', 'prefix: with name'],
        ];
    }

    /**
     * @dataProvider routeNames
     * @param string $route
     * @param string $expected
     */
    public function testRouteNames(string $route, string $expected): void
    {
        $uri = $this->router()->uri(RouteBuilder::routeName(static::NAMESPACE, $route));

        $this->assertSame($expected, $this->get($uri)->getBody()->__toString());
    }

    public function routeNames(): iterable
    {
        return [
            ['names.withoutName', 'name: without name'],
            ['with:name', 'name: with name'],
            ['prefix.withoutName', 'prefix: without name'],
            ['with:prefix:name', 'prefix: with name'],
        ];
    }
}
