<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(name="login", namespace="login")
 */
class LoginController
{
    /**
     * @Action(route="/login")
     */
    public function login(): string
    {
        return 'ok';
    }
}
