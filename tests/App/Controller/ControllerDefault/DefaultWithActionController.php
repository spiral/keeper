<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\ControllerDefault;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(namespace="controllerDefault2", name="cDefault2", prefix="/cdPrefix2", defaultAction="defaults")
 */
class DefaultWithActionController
{
    /**
     * @Action(route="/index", name="controllerDefault2:default:index")
     * @return string
     */
    public function defaults(): string
    {
        return 'controllerDefault2: defaults';
    }
}
