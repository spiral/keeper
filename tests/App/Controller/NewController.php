<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Views\ViewsInterface;

/**
 * @Controller(namespace="new", name="new", prefix="/new")
 */
class NewController
{
    /**
     * @Action(route="/new")
     * @param ViewsInterface $views
     * @return string
     */
    public function new(ViewsInterface $views): string
    {
        return $views->render('tests:new/new');
    }
}
