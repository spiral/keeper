<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Views\ViewsInterface;

/**
 * @Controller(name="auth", namespace="default")
 */
class AuthController
{
    /**
     * @Action(route="/view", name="auth:view")
     * @param ViewsInterface $views
     * @return string
     */
    public function view(ViewsInterface $views): string
    {
        return $views->render('tests:auth');
    }

    /**
     * @Action(route="/logout", name="auth:logout")
     */
    public function logout(): void
    {
    }
}
