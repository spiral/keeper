<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Keeper\Bootloader;

class ControllerDefaultBootloader extends Bootloader\KeeperBootloader
{
    protected const NAMESPACE = 'controllerDefault';
    protected const PREFIX    = '/controllerDefault';

    protected const LOAD = [
        Bootloader\AnnotatedBootloader::class,
        GuestBootloader::class,
    ];
}
