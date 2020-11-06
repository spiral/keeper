<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(name="users")
 */
class UserController
{
    /**
     * @Action(route="/list")
     */
    public function list(): string
    {
        return 'listed';
    }
}
