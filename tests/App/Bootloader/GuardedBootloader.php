<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Domain\GuardInterceptor;
use Spiral\Keeper\Bootloader;

class GuardedBootloader extends Bootloader\KeeperBootloader
{
    protected const NAMESPACE = 'guarded';
    protected const PREFIX    = '/guarded';

    protected const LOAD = [
        Bootloader\AnnotatedBootloader::class,
        GuestBootloader::class
    ];
}
