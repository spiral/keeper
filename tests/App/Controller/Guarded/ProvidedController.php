<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Guarded;

use Spiral\Domain\Annotation\Guarded;
use Spiral\Domain\Annotation\GuardNamespace;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

#[Controller(name: "provided", prefix: "/provided", namespace: "guarded")]
#[GuardNamespace(namespace: "guarded.provided")]
class ProvidedController
{
    #[Action(route: "/allowed")]
    #[Guarded(permission: "allowed")]
    public function allowed(): void
    {
    }

    #[Action(route: "/forbidden")]
    #[Guarded(permission: "forbidden")]
    public function forbidden(): void
    {
    }
}
