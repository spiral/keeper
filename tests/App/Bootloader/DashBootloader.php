<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Keeper\Bootloader;
use Spiral\Keeper\Bootloader\GuestBootloader;

class DashBootloader extends Bootloader\KeeperBootloader
{
    protected const NAMESPACE = 'dash';
    protected const PREFIX    = 'dash-';

    protected const LOAD = [
        Bootloader\SitemapBootloader::class,
        Bootloader\AnnotatedBootloader::class,
        GuestBootloader::class,
    ];
}
