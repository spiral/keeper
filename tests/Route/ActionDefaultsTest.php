<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Route;

use Spiral\Keeper\Helper\RouteBuilder;
use Spiral\Router\Exception\UndefinedRouteException;
use Spiral\Router\RouterInterface;
use Spiral\Tests\Keeper\HttpTrait;
use Spiral\Tests\Keeper\TestCase;

class ActionDefaultsTest extends TestCase
{
    use HttpTrait;

    protected const NAMESPACE = 'default';
    protected const PREFIX    = '/default';

    public function testUnknownControllerDefaultAction(): void
    {
        $request = $this->get(self::PREFIX . '/unknown');
        $this->assertSame(404, $request->getStatusCode());
    }

    public function testKnownControllerDefaultAction(): void
    {
        $uri = self::PREFIX . '/known';
        $request = $this->get($uri);
        $this->assertSame(200, $request->getStatusCode());
        $this->assertSame('known: foo', $request->getBody()->__toString());

        $this->assertSame($uri, (string)$this->router()->uri(RouteBuilder::routeName(static::NAMESPACE, 'known')));
    }

    public function testNotSetControllerDefaultActionWithIndex(): void
    {
        $this->assertSame(404, $this->get(self::PREFIX . '/notSetWithIndex')->getStatusCode());
        $this->assertSame(200, $this->get(self::PREFIX . '/notSetWithIndex/index')->getStatusCode());
    }

    public function testNoConfigDefaultController(): void
    {
        $this->expectException(UndefinedRouteException::class);
        $uri = (string)$this->router()->uri(RouteBuilder::routeName(static::NAMESPACE));
    }

    private function router(): RouterInterface
    {
        return $this->app->get(RouterInterface::class);
    }
}
