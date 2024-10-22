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

#[Controller(name: "root", prefix: "/root", namespace: "default")]
#[Group(name: "rootgroup")]
class RootController
{
    #[Link(title: "root", position: 0.7)]
    #[Action(route: "/self")]
    public function index(): void
    {
    }

    #[Link(title: "duplicated")]
    #[Action(route: "/duplicated")]
    public function duplicated(): void
    {
    }

    #[Action(route: "/child", name: "root:child")]
    #[Link(title: "child", parent: "parent")]
    #[Guarded(permission: "im-a-child")]
    public function child(Sitemap $sitemap, GuardInterface $guard, ServerRequestInterface $request): array
    {
        return iterator_to_array(
            $this->sitemap(
                $sitemap->withVisibleNodes($guard, $request->getAttribute(Router::ROUTE_NAME))
            )
        );
    }

    #[Guarded(permission: "parentRoot")]
    #[Link(title: "parent", parent: "custom.parent")]
    #[Action(route: "/parent", name: "root:parent")]
    public function parent(): void
    {
    }

    #[Action(route: "/top", name: "top")]
    #[Link(title: "parent", position: 0.6)]
    public function top(): void
    {
    }

    #[Action(route: "/bottom", name: "bottom")]
    #[Link(title: "bottom", parent: "top")]
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
