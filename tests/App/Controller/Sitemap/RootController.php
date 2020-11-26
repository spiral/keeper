<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Sitemap;

use Psr\Http\Message\ServerRequestInterface;
use Spiral\Domain\Annotation\Guarded;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Keeper\Annotation\Sitemap\Group;
use Spiral\Keeper\Annotation\Sitemap\Link;
use Spiral\Keeper\Module\Sitemap;
use Spiral\Router\Router;
use Spiral\Security\GuardInterface;

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
     * @param Sitemap                $sitemap
     * @param GuardInterface         $guard
     * @param ServerRequestInterface $request
     * @return array
     */
    public function child(Sitemap $sitemap, GuardInterface $guard, ServerRequestInterface $request): array
    {
        return iterator_to_array(
            $this->sitemap(
                $sitemap->withVisibleNodes($guard, $request->getAttribute(Router::ROUTE_NAME))
            )
        );
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

    private function sitemap(Sitemap $sitemap): \Generator
    {
        foreach ($sitemap->getActivePath() as $name => $node) {
            yield $name => $this->wrap($node);
        }
    }

    private function wrap(Sitemap\Node $node): array
    {
        $nodes = [];
        foreach ($node->getIterator() as $name => $child) {
            $nodes[$name] = $this->wrap($child);
        }
        return [
            'name'    => $node->getName(),
            'options' => $node->getOptions(),
            'nodes'   => $nodes
        ];
    }
}
