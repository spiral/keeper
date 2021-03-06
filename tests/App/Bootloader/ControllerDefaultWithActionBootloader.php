<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Keeper\Bootloader;

class ControllerDefaultWithActionBootloader extends Bootloader\KeeperBootloader
{
    protected const NAMESPACE = 'controllerDefault2';
    protected const PREFIX    = '/controllerDefault2';

    protected const LOAD = [
        Bootloader\AnnotatedBootloader::class,
        GuestBootloader::class,
    ];
}
