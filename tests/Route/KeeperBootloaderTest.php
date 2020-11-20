<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Route;

use Spiral\Keeper\Helper\RouteBuilder;
use Spiral\Router\RouterInterface;
use Spiral\Tests\Keeper\HttpTrait;
use Spiral\Tests\Keeper\TestCase;

class KeeperBootloaderTest extends TestCase
{
    use HttpTrait;

    public function testInterceptors(): void
    {
        $response = $this->get((string)$this->router()->uri(RouteBuilder::routeName('i[i:check]')));
        $output = json_decode((string)$response->getBody(), true);
        $this->assertIsArray($output);
        $this->assertCount(3, $output);
        $this->assertContains('one', $output);
        $this->assertContains('two', $output);
        $this->assertContains('three', $output);
    }

    private function router(): RouterInterface
    {
        return $this->app->get(RouterInterface::class);
    }
}
