<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Keeper\Bootloader;
use Spiral\Keeper\Bootloader\GuestBootloader;

class InterceptedBootloader extends Bootloader\KeeperBootloader
{
    protected const NAMESPACE = 'i';
    protected const PREFIX    = '/i';
    protected const CONFIG_NAME = 'intercepted';

    protected const LOAD = [
        Bootloader\AnnotatedBootloader::class,
        GuestBootloader::class,
    ];
}
