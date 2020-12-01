<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(namespace="m", prefix="/m", name="m")
 */
class MiddlewaredController
{
    /**
     * @Action(route="/check", name="m:check")
     */
    public function check(): string
    {
        return __METHOD__;
    }
}
