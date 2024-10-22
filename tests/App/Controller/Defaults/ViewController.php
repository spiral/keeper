<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Defaults;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Views\ViewsInterface;

#[Controller(name: "view", prefix: "/view", namespace: "default")]
class ViewController
{
    #[Action(route: "/tabs/false", name: "view:tabs:false")]
    public function false(ViewsInterface $views): string
    {
        return $views->render('tests:tabs', ['condition' => false]);
    }

    #[Action(route: "/tabs/true", name: "view:tabs:true")]
    public function true(ViewsInterface $views): string
    {
        return $views->render('tests:tabs', ['condition' => true]);
    }
}
