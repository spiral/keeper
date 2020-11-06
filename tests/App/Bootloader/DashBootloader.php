<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Boot\BootloadManager;
use Spiral\Keeper\Bootloader;
use Spiral\Keeper\Bootloader\GuestBootloader;
use Spiral\Router\RouterInterface;

class DashBootloader extends Bootloader\KeeperBootloader
{
    protected const NAMESPACE = 'dash';
    protected const PREFIX    = 'dash-';

    protected const LOAD = [
        Bootloader\SitemapBootloader::class,
        Bootloader\AnnotatedBootloader::class,
        GuestBootloader::class,
    ];

    public function boot(BootloadManager $bootloadManager, RouterInterface $appRouter): void
    {
        parent::boot($bootloadManager, $appRouter);
    }
}
