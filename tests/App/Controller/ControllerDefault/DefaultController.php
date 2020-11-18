<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\ControllerDefault;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(namespace="controllerDefault", name="cDefault", prefix="/cdPrefix")
 */
class DefaultController
{
    /**
     * @Action(route="/index", name="controllerDefault:default:index")
     * @return string
     */
    public function defaults(): string
    {
        return 'controllerDefault: defaults';
    }
}
