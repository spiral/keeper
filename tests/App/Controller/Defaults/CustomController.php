<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Defaults;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

#[Controller(name: "custom", namespace: "default")]
class CustomController
{
    #[Action(route: "/parent")]
    public function parent(): void
    {
    }
}
