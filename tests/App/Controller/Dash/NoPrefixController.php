<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Dash;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(name="users", namespace="dash", defaultAction="defaulted")
 */
class NoPrefixController
{
    /**
     * @Action(route="boo", name="users:list")
     */
    public function list(): string
    {
        return 'np:' . __FUNCTION__;
    }

    /**
     * @Action(route="def", name="users:def")
     */
    public function defaulted(): string
    {
        return 'np:' . __FUNCTION__;
    }

    /**
     * @Action(route="dot")
     */
    public function dot(): string
    {
        return 'np:' . __FUNCTION__;
    }
}
