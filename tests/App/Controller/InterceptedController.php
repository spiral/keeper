<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(namespace="i", prefix="/i", name="i")
 */
class InterceptedController
{
    /**
     * @Action(route="/check", name="i:check")
     */
    public function check(): string
    {
        return __METHOD__;
    }
}
