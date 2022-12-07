<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Core\Container\Autowire;
use Spiral\Keeper\Bootloader;
use Spiral\Keeper\Middleware\LoginMiddleware;

class LoginBootloader extends Bootloader\KeeperBootloader
{
    protected const NAMESPACE = 'login';
    protected const PREFIX    = '/login';

    protected const LOAD       = [
        Bootloader\AnnotatedBootloader::class,
        Bootloader\GuestBootloader::class,
    ];
    protected const MIDDLEWARE = [LoginMiddleware::class];

    protected function getMiddleware(): array
    {
        return [
            new Autowire(LoginMiddleware::class, ['loginView' => 'tests:login'])
        ];
    }
}
