<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Bootloader;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Domain\Annotation\Guarded;
use Spiral\Helpers\GraphSorter;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Keeper\Annotation\Sitemap\Group;
use Spiral\Keeper\Annotation\Sitemap\Link;
use Spiral\Keeper\Annotation\Sitemap\Segment;
use Spiral\Keeper\Annotation\Sitemap\View;
use Spiral\Keeper\Helper\Locator;
use Spiral\Keeper\Module\Sitemap;

class SitemapBootloader extends Bootloader
{
    protected const ROOT = Sitemap::TYPE_ROOT;

    /** @var AnnotationReader */
    private $reader;
    /** @var Locator */
    private $locator;
    /** @var GraphSorter */
    private $sorter;

    public function __construct(AnnotationReader $reader, Locator $locator)
    {
        $this->reader = $reader;
        $this->locator = $locator;

        $this->initSorter();
    }

    final public function boot(KeeperBootloader $keeper): void
    {
        $sitemap = new Sitemap($keeper->getNamespace());
        $keeper->addModule($sitemap, ['sitemap']);

        $this->declareSitemap($sitemap);
        $this->fillFromSitemap($sitemap);

        AnnotationRegistry::registerLoader('class_exists');
        $annotations = $this->parseAnnotations($keeper->getNamespace(), $sitemap);

        foreach ($annotations as $annotation) {
            $this->setSitemap($sitemap, $annotation);
        }
    }

    protected function declareSitemap(Sitemap $sitemap): void
    {
        // Your code goes here
    }

    private function initSorter(): void
    {
        $this->sorter = new GraphSorter();
        $this->sorter->addItem(static::ROOT, ['type' => Sitemap::TYPE_ROOT, 'name' => static::ROOT, 'child' => []], []);
    }

    private function fillFromSitemap(Sitemap $sitemap): void
    {
        if (empty($sitemap->getElements())) {
            return;
        }

        foreach ($sitemap->getIterator() as $node) {
            $this->fillFromNode($node);
        }
    }

    private function fillFromNode(Sitemap\Node $node, Sitemap\Node $parent = null): void
    {
        $this->sorter->addItem(
            $node->getName(),
            [
                'type'    => $node->getOption('type'),
                'name'    => $node->getName(),
                'parent'  => $parent ? $parent->getName() : static::ROOT,
                'title'   => $node->getOption('title'),
                'options' => $node->getOptions(),
                'child'   => []
            ],
            [$parent === null ? static::ROOT : $parent->getName()]
        );
        foreach ($node->getIterator() as $child) {
            $this->fillFromNode($child, $node);
        }
    }

    private function parseAnnotations(string $namespace, Sitemap $sitemap): iterable
    {
        $lastSegments = [];
        $methods = [];
        /**
         * @var \ReflectionClass $class
         * @var Controller       $controller
         */
        foreach ($this->locator->locateNamespaceControllers($namespace) as $class => $controller) {
            $lastSegments[$controller->name] = $this->buildClassSitemap($class);
            foreach ($this->packMethods($namespace, $class, $controller->name) as $name => $method) {
                $methods[$name] = $method;
            }
        }

        $this->buildSitemap($lastSegments, $methods, $sitemap);
        yield from $this->sort();
    }

    private function buildClassSitemap(\ReflectionClass $class): string
    {
        $lastSegment = static::ROOT;
        foreach ($this->reader->getClassAnnotations($class) as $ann) {
            switch (true) {
                case $ann instanceof Segment:
                case $ann instanceof Group:
                    $lastSegment = $ann->name;
                    $this->sorter->addItem(
                        $ann->name,
                        [
                            'type'    => $ann instanceof Segment ? Sitemap::TYPE_SEGMENT : Sitemap::TYPE_GROUP,
                            'name'    => $ann->name,
                            'parent'  => $ann->parent,
                            'title'   => $ann->title,
                            'options' => $ann->options,
                            'child'   => []
                        ],
                        [$ann->parent]
                    );
            }
        }
        return $lastSegment;
    }

    private function packMethods(string $namespace, \ReflectionClass $class, string $controller): iterable
    {
        /**
         * @var \ReflectionMethod $method
         * @var Action            $action
         * @var Guarded|null      $permission
         */
        foreach ($this->locator->locateMethodsWithAction($class) as $method => $action) {
            $permission = $this->reader->getMethodAnnotation($method, Guarded::class);
            $method = Sitemap\Method::create($namespace, $controller, $method, $action, $permission);
            yield "$controller.{$method->name}" => $method;
        }
    }

    /**
     * @param array            $lastSegments
     * @param Sitemap\Method[] $methods
     * @param Sitemap          $sitemap
     */
    private function buildSitemap(array $lastSegments, array $methods, Sitemap $sitemap): void
    {
        $sitemapElements = $sitemap->getElements();
        foreach ($methods as $method) {
            $lastSegment = $lastSegments[$method->controller] ?? static::ROOT;
            foreach ($this->reader->getMethodAnnotations($method->reflection) as $ann) {
                switch (true) {
                    case $ann instanceof Link:
                    case $ann instanceof View:
                        $parent = null;
                        if ($ann->hasAbsoluteParent()) {
                            $parent = $ann->parent;
                        } elseif ($ann->hasRelativeParent()) {
                            $parent = "{$method->controller}.{$ann->parent}";
                        }

                        $knownParent = $parent && (
                            isset($methods[$parent]) ||
                            in_array($parent, $sitemapElements, true)
                        );
                        if (!$knownParent) {
                            $parent = $lastSegment;
                        }

                        $this->sorter->addItem(
                            $method->name(),
                            [
                                'type'    => $ann instanceof Link ? Sitemap::TYPE_LINK : Sitemap::TYPE_VIEW,
                                'name'    => $method->name(),
                                'parent'  => $parent,
                                'title'   => $ann->title,
                                'options' => $ann->options + [
                                        'permission' => $method->permission,
                                        'route'      => $method->route
                                    ],
                                'child'   => []
                            ],
                            $parent ? [$parent] : []
                        );

                        break;
                }
            }
        }
    }

    private function sort()
    {
        // wood working
        $root = null;
        $points = [];
        foreach ($this->sorter->sort() as $item) {
            $points[$item['name']] = &$item;
            if ($root === null) {
                $root = &$item;
                unset($item);
                continue;
            }

            $points[$item['parent']]['child'][] = &$item;
            unset($item);
        }

        return $root['child'];
    }

    private function setSitemap(Sitemap $sitemap, array $item): void
    {
        $node = $sitemap->{$item['type']}($item['name'], $item['title'], $item['options']);

        foreach ($item['child'] as $child) {
            $this->setSitemap($node, $child);
        }
    }
}
