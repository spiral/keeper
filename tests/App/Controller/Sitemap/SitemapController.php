<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Sitemap;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Keeper\Module\Sitemap;

/**
 * @Controller(
 *     name="sitemap",
 *     prefix="/sitemap",
 *     namespace="default"
 * )
 */
class SitemapController
{
    /**
     * @Action(route="/")
     * @param Sitemap $sitemap
     * @return array
     */
    public function index(Sitemap $sitemap): array
    {
        return iterator_to_array($this->sitemap($sitemap));
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
            'nodes'   => $nodes
        ];
    }
}
