<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Defaults;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Views\ViewsInterface;

/**
 * @Controller(namespace="default", name="view", prefix="/view")
 */
class ViewController
{
    /**
     * @Action(route="/tabs/false", name="view:tabs:false")
     * @param ViewsInterface $views
     * @return string
     */
    public function false(ViewsInterface $views): string
    {
        return $views->render('tests:tabs', ['condition' => false]);
    }

    /**
     * @Action(route="/tabs/true", name="view:tabs:false")
     * @param ViewsInterface $views
     * @return string
     */
    public function true(ViewsInterface $views): string
    {
        return $views->render('tests:tabs', ['condition' => true]);
    }
}
