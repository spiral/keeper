<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Defaults;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

#[Controller(name: "notSetWithIndex", prefix: "/notSetWithIndex", namespace: "default")]
class NotSetWithIndexController
{
    #[Action(route: "/index")]
    public function index(): string
    {
        return 'notSetWithIndex: index';
    }
}
