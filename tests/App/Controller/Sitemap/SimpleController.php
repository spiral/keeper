<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Sitemap;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Keeper\Annotation\Sitemap\Link;

/**
 * @Controller(namespace="default", name="simple")
 */
class SimpleController
{
    /**
     * @Action(name="some:simple:method", route="")
     * @Link(title="simple")
     */
    public function method(): void
    {
    }
}
