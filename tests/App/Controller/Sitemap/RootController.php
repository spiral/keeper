<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Sitemap;

use Spiral\Domain\Annotation\Guarded;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Keeper\Annotation\Sitemap\Group;
use Spiral\Keeper\Annotation\Sitemap\Link;
use Spiral\Keeper\Module\Sitemap;

/**
 * @Controller(
 *     name="dashboard",
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
     * @param Sitemap $sitemap
     * @return array
     */
    public function index(Sitemap $sitemap): array
    {
        return iterator_to_array($this->sitemap($sitemap));
    }

    /**
     * @Guarded(permission="im-a-child")
     * @Link(title="child", parent="parent")
     * @Action(route="/child", name="root:child")
     * @return string
     */
    public function child(): string
    {
        return 'child';
    }

    /**
     * @Link(title="parent", parent="custom.parent")
     * @Action(route="/parent", name="root:parent")
     * @return string
     */
    public function parent(): string
    {
        return 'parent';
    }

    private function sitemap(Sitemap $sitemap): \Generator
    {
        foreach ($sitemap->getIterator() as $name => $node) {
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
            'nodes'   => $nodes ?: null
        ];
    }
}
