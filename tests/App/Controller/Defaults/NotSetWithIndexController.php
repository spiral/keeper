<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Defaults;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(namespace="default", name="notSetWithIndex", prefix="/notSetWithIndex")
 */
class NotSetWithIndexController
{
    /**
     * @Action(route="/index")
     * @return string
     */
    public function index(): string
    {
        return 'notSetWithIndex: index';
    }
}
