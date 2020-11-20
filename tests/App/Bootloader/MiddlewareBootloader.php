<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Keeper\Bootloader\KeeperBootloader;
use Spiral\Tests\Keeper\App\Middleware\Three;

class MiddlewareBootloader extends Bootloader
{
    public function boot(KeeperBootloader $keeper): void
    {
        $keeper->addMiddleware(Three::class);
    }
}
