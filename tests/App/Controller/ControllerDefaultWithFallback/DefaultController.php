<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\ControllerDefaultWithFallback;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(namespace="controllerDefault3", name="cDefault3", prefix="/cdPrefix3")
 */
class DefaultController
{
    /**
     * @Action(route="/index", name="controllerDefault3:default:index")
     * @return string
     */
    public function index(): string
    {
        return 'controllerDefault3: defaults';
    }
}
