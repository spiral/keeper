<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Keeper\Bootloader;

class BlankBootloader extends Bootloader\KeeperBootloader
{
    protected const NAMESPACE = 'blank';
    protected const PREFIX    = '';

    protected const LOAD = [
        Bootloader\AnnotatedBootloader::class,
        GuestBootloader::class,
    ];
}
