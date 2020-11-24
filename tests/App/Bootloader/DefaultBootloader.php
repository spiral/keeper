<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Keeper\Bootloader;
use Spiral\Keeper\Bootloader\GuestBootloader;

class DefaultBootloader extends Bootloader\KeeperBootloader
{
    protected const NAMESPACE = 'default';
    protected const PREFIX    = '/default';

    protected const LOAD = [
        Bootloader\AnnotatedBootloader::class,
        GuestBootloader::class,
        SitemapBootloader::class
    ];
}
