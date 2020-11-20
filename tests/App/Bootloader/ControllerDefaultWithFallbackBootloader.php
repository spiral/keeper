<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Keeper\Bootloader;
use Spiral\Keeper\Bootloader\GuestBootloader;

class ControllerDefaultWithFallbackBootloader extends Bootloader\KeeperBootloader
{
    protected const NAMESPACE = 'controllerDefault3';
    protected const PREFIX    = '/controllerDefault3';

    protected const LOAD = [
        Bootloader\AnnotatedBootloader::class,
        GuestBootloader::class,
    ];
}
