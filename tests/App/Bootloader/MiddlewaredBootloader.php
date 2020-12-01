<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Keeper\Bootloader;
use Spiral\Tests\Keeper\App\Middleware;

class MiddlewaredBootloader extends Bootloader\KeeperBootloader
{
    protected const NAMESPACE = 'm';
    protected const PREFIX    = '/m';

    protected const LOAD       = [
        Bootloader\AnnotatedBootloader::class,
        MiddlewareBootloader::class,
        GuestBootloader::class,
    ];
    protected const MIDDLEWARE = [
        Middleware\One::class,
        Middleware\Two::class
    ];
}
