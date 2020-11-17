<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App;

use Spiral\Bootloader\CommandBootloader;
use Spiral\Bootloader\Http\DiactorosBootloader;
use Spiral\Bootloader\Http\RouterBootloader;
use Spiral\Bootloader\Security\GuardBootloader;
use Spiral\Console\Console;
use Spiral\Framework\Kernel;
use Spiral\Http\Http;
use Spiral\Stempler\Bootloader\StemplerBootloader;
use Spiral\Tests\Keeper\App\Bootloader;

class App extends Kernel
{
    protected const LOAD = [
        StemplerBootloader::class,
        GuardBootloader::class,
        DiactorosBootloader::class,
        RouterBootloader::class,
    ];

    protected const APP = [
        CommandBootloader::class,
        Bootloader\AnnotationBootloader::class,
        Bootloader\BlankBootloader::class,
    ];

    public function getHttp(): Http
    {
        return $this->container->get(Http::class);
    }

    public function getConsole(): Console
    {
        return $this->container->get(Console::class);
    }

    public function get(string $class)
    {
        return $this->container->get($class);
    }
}