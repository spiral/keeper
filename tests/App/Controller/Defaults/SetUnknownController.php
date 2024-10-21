<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Defaults;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

#[Controller(name: "unknown", prefix: "/unknown", namespace: "default", defaultAction: "unknown")]
class SetUnknownController
{
    #[Action(route: "/baz")]
    public function foo(): string
    {
        return 'unknown: foo';
    }
}
