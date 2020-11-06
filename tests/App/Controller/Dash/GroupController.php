<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Dash;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(name="groups", namespace="dash", prefix="groups/")
 */
class GroupController
{
    /**
     * @Action(route="boo", name="groups:list")
     */
    public function list(): string
    {
        return 'g:' . __FUNCTION__;
    }

    /**
     * @Action(route="foo", name="groups:dot")
     */
    public function dot(): string
    {
        return 'g:' . __FUNCTION__;
    }
}
