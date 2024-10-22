<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Guarded;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

#[Controller(name: "missing", prefix: "/missing", namespace: "guarded")]
class MissingController
{
    #[Action(route: "/allowed")]
    public function allowed(): void
    {
    }

    #[Action(route: "/forbidden")]
    public function forbidden(): void
    {
    }
}
