<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Route;

use Spiral\Keeper\Helper\RouteBuilder;
use Spiral\Router\Exception\UndefinedRouteException;
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

    /**
     * Tests that no weird defaults are passed to the route (given default is taken from the target).
     */
    public function testDefaults(): void
    {
        $uri = self::PREFIX . '/known/defaults';
        $request = $this->get($uri);
        $this->assertSame(200, $request->getStatusCode());
        $this->assertSame(['action' => 'defaults'], json_decode((string)$request->getBody(), true));
    }
}
