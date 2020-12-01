<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Defaults;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(namespace="default", name="unknown", prefix="/unknown", defaultAction="unknown")
 */
class SetUnknownController
{
    /**
     * @Action(route="/baz")
     * @return string
     */
    public function foo(): string
    {
        return 'unknown: foo';
    }
}
