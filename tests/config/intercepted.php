<?php

declare(strict_types=1);

use Spiral\Core\Container\Autowire;
use Spiral\Tests\Keeper\App\Interceptor;

return [
    'interceptors' => [
        Interceptor\One::class,
        new Autowire(Interceptor\Two::class),
        new Interceptor\Three()
    ],
];
