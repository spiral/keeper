<?php

/**
 * This file is part of Spiral Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Tests\Keeper;

use Spiral\Bootloader\CommandBootloader;
use Spiral\Bootloader\Http\ErrorHandlerBootloader;
use Spiral\Bootloader\Http\RouterBootloader;
use Spiral\Bootloader\Security\GuardBootloader;
use Spiral\Bootloader\Views\TranslatedCacheBootloader;
use Spiral\Keeper\Bootloader\UIBootloader;
use Spiral\Nyholm\Bootloader\NyholmBootloader;
use Spiral\Router\RouterInterface;
use Spiral\Stempler\Bootloader\StemplerBootloader;
use Spiral\Tests\Keeper\App\Bootloader;

abstract class TestCase extends \Spiral\Testing\TestCase
{
    public function defineBootloaders(): array
    {
        return [
            // Load
            ErrorHandlerBootloader::class,
            TranslatedCacheBootloader::class,
            StemplerBootloader::class,
            GuardBootloader::class,
            NyholmBootloader::class,
            RouterBootloader::class,
            CommandBootloader::class,

            // ??
            \Spiral\Keeper\Bootloader\KeeperBootloader::class,

            // App
            Bootloader\AnnotationBootloader::class,
            Bootloader\AppBootloader::class,
            Bootloader\BlankBootloader::class,
            Bootloader\DefaultBootloader::class,
            Bootloader\ControllerDefaultBootloader::class,
            Bootloader\ControllerDefaultWithActionBootloader::class,
            Bootloader\ControllerDefaultWithFallbackBootloader::class,
            Bootloader\OldBootloader::class,
            Bootloader\NewBootloader::class,
            Bootloader\InterceptedBootloader::class,
            Bootloader\MiddlewaredBootloader::class,
            Bootloader\GuardedBootloader::class,
            UIBootloader::class,
            Bootloader\LoginBootloader::class,
            Bootloader\RoutesBootloader::class,
        ];
    }

    public function rootDirectory(): string
    {
        return \dirname(__DIR__ . '/App/');
    }

    public function defineDirectories(string $root): array
    {
        return [
            'config' => __DIR__ . '/config/',
            'views' => __DIR__ . '/views/',
            'app' => __DIR__ . '/App/',
        ] + parent::defineDirectories($root);
    }

    protected function router(): RouterInterface
    {
        return $this->getContainer()->get(RouterInterface::class);
    }
}
