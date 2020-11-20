<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Keeper\Bootloader;
use Spiral\Keeper\Bootloader\GuestBootloader;

class OldBootloader extends Bootloader\KeeperBootloader
{
    protected const NAMESPACE = 'keeper';
    protected const PREFIX    = '/old/';

    protected const LOAD      = [
        Bootloader\SitemapBootloader::class,
        Bootloader\AnnotatedBootloader::class,
        GuestBootloader::class,
    ];
}
