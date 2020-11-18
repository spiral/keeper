<?php

declare(strict_types=1);

use Spiral\Tests\Keeper\App\Controller\ControllerDefault\DefaultController;

return [
    'routeDefaults' => [
        'controller' => DefaultController::class,
        'action'     => 'defaults',
    ]
];
