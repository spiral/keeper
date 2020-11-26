<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Views\ViewsInterface;

/**
 * @Controller(namespace="keeper", name="old", prefix="/old")
 */
class OldController
{
    /**
     * @Action(route="/old")
     * @param ViewsInterface $views
     * @return string
     */
    public function old(ViewsInterface $views): string
    {
        return $views->render('tests:old/old');
    }

    /**
     * @Action(route="/invalid")
     * @param ViewsInterface $views
     * @return string
     */
    public function invalid(ViewsInterface $views): string
    {
        return $views->render('tests:old/invalid');
    }

    /**
     * @Action(route="/new")
     * @param ViewsInterface $views
     * @return string
     */
    public function new(ViewsInterface $views): string
    {
        return $views->render('tests:old/new');
    }
}
