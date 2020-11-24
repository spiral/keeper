<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Sitemap;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Keeper\Annotation\Sitemap\Group;
use Spiral\Keeper\Annotation\Sitemap\Link;

/**
 * @Controller(
 *     name="external",
 *     prefix="/external",
 *     namespace="default"
 * )
 * @Group(name="externalgroup")
 */
class ExternalController
{
    /**
     * @Link(title="external", parent="dashboard.child")
     * @Action(route="", name="external:self")
     * @return string
     */
    public function index(): string
    {
        return 'external';
    }

    /**
     * @Link(title="external", parent="custom.parent")
     * @Action(route="", name="external:custom")
     * @return string
     */
    public function custom(): string
    {
        return 'custom';
    }
}
