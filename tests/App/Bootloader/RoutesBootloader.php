<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Psr\Http\Server\MiddlewareInterface;
use Spiral\Bootloader\Http\RoutesBootloader as BaseRoutesBootloader;
use Spiral\Debug\StateCollector\HttpCollector;

final class RoutesBootloader extends BaseRoutesBootloader
{
    protected function globalMiddleware(): array
    {
        return [
            HttpCollector::class
        ];
    }

    /**
     * @return array<non-empty-string, list<class-string<MiddlewareInterface>>>
     */
    protected function middlewareGroups(): array
    {
        return [
            'web' => []
        ];
    }
}
