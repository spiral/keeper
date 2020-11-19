<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\DirectoriesInterface;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Config\Patch\Append;

class AppBootloader extends Bootloader
{
    public function boot(ConfiguratorInterface $config, DirectoriesInterface $directories): void
    {
        $config->modify(
            'views',
            new Append('namespaces', 'tests', $directories->get('views'))
        );
    }
}
