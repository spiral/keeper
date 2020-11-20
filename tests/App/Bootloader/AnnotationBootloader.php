<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Keeper\Bootloader;
use Spiral\Keeper\Bootloader\GuestBootloader;

class AnnotationBootloader extends Bootloader\KeeperBootloader
{
    protected const NAMESPACE = 'annotation';
    protected const PREFIX    = '/annotation_';

    protected const LOAD = [
        Bootloader\AnnotatedBootloader::class,
        GuestBootloader::class,
    ];
}
