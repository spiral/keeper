<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Keeper\Bootloader;

class NewBootloader extends Bootloader\KeeperBootloader
{
    protected const NAMESPACE = 'new';
    protected const PREFIX    = '/new';

    protected const LOAD = [
        Bootloader\AnnotatedBootloader::class,
        GuestBootloader::class,
    ];
}
