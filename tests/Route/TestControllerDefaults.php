<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Route;

use Spiral\Keeper\Helper\RouteBuilder;
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

        $names = $this->names();
        //has controller+action defaults
        $this->assertContains('controllerDefault', $names);
        $this->assertContains(RouteBuilder::routeName('controllerDefault', 'cDefault.defaults'), $names);
        $this->assertContains(RouteBuilder::routeName('controllerDefault', 'controllerDefault:default:defaults'), $names);
    }

    public function testHasConfigDefaultControllerWithOwnDefaultAction(): void
    {
        $response = $this->get('/controllerDefault2');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertEquals('controllerDefault2: defaults', (string)$response->getBody());

        $names = $this->names();
        //has controller/controller+action defaults
        $this->assertContains('controllerDefault2', $names);
        $this->assertContains(RouteBuilder::routeName('controllerDefault2', 'cDefault2'), $names);
        $this->assertContains(RouteBuilder::routeName('controllerDefault2', 'cDefault2.defaults'), $names);
        $this->assertContains(RouteBuilder::routeName('controllerDefault2', 'controllerDefault2:default:index'), $names);
    }

    public function testHasConfigDefaultControllerWithFallbackAction(): void
    {
        $response = $this->get('/controllerDefault3');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertEquals('controllerDefault3: defaults', (string)$response->getBody());

        $names = $this->names();
        //has controller+index(fallback) defaults
        $this->assertContains('controllerDefault3', $names);
        $this->assertContains(RouteBuilder::routeName('controllerDefault3', 'cDefault3.index'), $names);
        $this->assertContains(RouteBuilder::routeName('controllerDefault3', 'controllerDefault3:default:index'), $names);
    }

    private function names(): array
    {
        return array_unique(array_keys($this->router()->getRoutes()));
    }

    private function router(): RouterInterface
    {
        return $this->app->get(RouterInterface::class);
    }
}
