<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Defaults;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(namespace="default", name="custom")
 */
class CustomController
{
    /**
     * @Action(route="/parent")
     */
    public function parent(): void
    {
    }
}
