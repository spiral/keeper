<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Sitemap;

use Spiral\Domain\Annotation\Guarded;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Keeper\Annotation\Sitemap\Group;
use Spiral\Keeper\Annotation\Sitemap\Link;

/**
 * @Controller(
 *     name="root",
 *     prefix="/root",
 *     namespace="default"
 * )
 * @Group(name="rootgroup")
 */
class RootController
{
    /**
     * @Link(title="root")
     * @Action(route="/self")
     */
    public function index(): void
    {
    }

    /**
     * @Guarded(permission="im-a-child")
     * @Link(title="child", parent="parent")
     * @Action(route="/child", name="root:child")
     */
    public function child(): void
    {
    }

    /**
     * @Link(title="parent", parent="custom.parent")
     * @Action(route="/parent", name="root:parent")
     */
    public function parent(): void
    {
    }

    /**
     * @Link(title="parent")
     * @Action(route="/top", name="top")
     */
    public function top(): void
    {
    }

    /**
     * @Link(title="bottom", parent="top")
     * @Action(route="/bottom", name="bottom")
     */
    public function bottom(): void
    {
    }
}
