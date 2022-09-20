<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Route;

use Spiral\Core\Container;
use Spiral\Keeper\Config\KeeperConfig;
use Spiral\Keeper\Module\RouteRegistry;
use Spiral\Router\GroupRegistry;
use Spiral\Tests\Keeper\TestCase;

class RegistryTest extends TestCase
{
    /**
     * @dataProvider invalidProvider
     * @param string $namespace
     * @param mixed  $route
     * @param array  $parameters
     */
    public function testInvalidLegacy(string $namespace, $route, array $parameters): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->registry()->uri($namespace, $route, $parameters);
    }

    public function invalidProvider(): iterable
    {
        return [
            ['', 123, ['a' => 'b']],
            ['', ['c' => 'd'], ['a' => 'b']],
        ];
    }

    /**
     * @dataProvider validProvider
     * @param array  $query
     * @param string $namespace
     * @param        $route
     * @param array  $parameters
     */
    public function testValidLegacy(array $query, string $namespace, $route, array $parameters = []): void
    {
        $url = '/old/old/old';
        if ($query) {
            $url .= '?' . http_build_query($query);
        }
        $this->assertSame($url, $this->registry()->uri($namespace, $route, $parameters));
    }

    public function validProvider(): iterable
    {
        return [
            [[], 'keeper', 'old.old'],
            [[], 'old.old', null],
            [['a' => 'b'], 'keeper', 'old.old', ['a' => 'b']],
            [['a' => 'b'], 'old.old', ['a' => 'b']],
        ];
    }

    private function registry(): RouteRegistry
    {
        return new RouteRegistry(
            new KeeperConfig('ns', ['middleware' => []]), $this->router(), new GroupRegistry(new Container())
        );
    }
}
