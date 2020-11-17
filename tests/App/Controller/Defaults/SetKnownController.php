<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Defaults;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(namespace="default", name="known", prefix="/known", defaultAction="foo")
 */
class SetKnownController
{
    /**
     * @Action(route="/baz")
     * @return string
     */
    public function foo(): string
    {
        return 'known: foo';
    }
}
