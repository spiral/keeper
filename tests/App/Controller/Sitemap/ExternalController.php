<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Sitemap;

use Spiral\Domain\Annotation\Guarded;
use Spiral\Domain\Annotation\GuardNamespace;
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
 * @GuardNamespace(namespace="default.external")
 * @Group(name="externalgroup")
 */
class ExternalController
{
    /**
     * @Link(title="external", parent="root.child")
     * @Action(route="", name="external:self")
     */
    public function index(): void
    {
    }

    /**
     * @Guarded(permission="cstm")
     * @Link(title="external", parent="custom.parent")
     * @Action(route="/custom", name="external:custom")
     */
    public function custom(): void
    {
    }

    /**
     * @Guarded(permission="linkAllowed")
     * @Link(title="external", parent="custom.parent", permission="linkForbidden")
     * @Action(route="/forbidden", name="external:forbidden")
     */
    public function forbidden(): void
    {
    }
}
