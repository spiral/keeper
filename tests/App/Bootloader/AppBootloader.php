<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\DirectoriesInterface;
use Spiral\Bootloader\Views\ViewsBootloader;

class AppBootloader extends Bootloader
{
    public function boot(DirectoriesInterface $directories, ViewsBootloader $views): void
    {
        $views->addDirectory('tests', $directories->get('views'));
        $views->addDirectory('keeper', $directories->get('views') . '/../../views/');
    }
}
