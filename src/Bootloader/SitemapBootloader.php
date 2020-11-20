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

final class SitemapBootloader extends Bootloader
{
    /** @var AnnotationReader */
    private $reader;
    /** @var Locator */
    private $locator;
    /** @var GraphSorter */
    private $sorter;

    public function __construct(AnnotationReader $reader, Locator $locator, GraphSorter $sorter)
    {
        $this->reader = $reader;
        $this->locator = $locator;
        $this->sorter = $sorter;
    }

    public function boot(KeeperBootloader $keeper): void
    {
        $sitemap = new Sitemap($keeper->getNamespace());
        $keeper->addModule($sitemap, ['sitemap']);
        AnnotationRegistry::registerLoader('class_exists');

        $annotations = $this->parseAnnotations($keeper->getNamespace());
        foreach ($annotations as $annotation) {
            $this->setSitemap($sitemap, $annotation);
        }
    }

    private function parseAnnotations(string $namespace): iterable
    {
        $lastSegments = [];
        $methods = [];
        /**
         * @var \ReflectionClass $class
         * @var Controller       $controller
         */
        foreach ($this->locator->locateNamespaceControllers($namespace) as $class => $controller) {
            $lastSegments[$controller->name] = $this->buildClassSitemap($class);
            foreach ($this->packMethods($class, $namespace, $controller->name) as $name => $method) {
                $methods[$name] = $method;
            }
        }

        yield from $this->buildSitemap($lastSegments, $methods);
    }

    private function packMethods(\ReflectionClass $class, string $namespace, string $controller): \Generator
    {
        /**
         * @var \ReflectionMethod $method
         * @var Action            $action
         */
        foreach ($this->locator->locateMethodsWithAction($class) as $method => $action) {
            /** @var Guarded|null $permission */
            $permission = $this->reader->getMethodAnnotation($method, Guarded::class);

            $method = Sitemap\Method::create($namespace, $class->getName(), $controller, $method, $action, $permission);
            yield "$controller.{$method->name}" => $method;
        }
    }

    private function buildClassSitemap(\ReflectionClass $class): string
    {
        $lastSegment = 'root';
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

    /**
     * @param array            $lastSegments
     * @param Sitemap\Method[] $methods
     * @return array
     */
    private function buildSitemap(array $lastSegments, array $methods): array
    {
        $this->sorter->addItem('root', ['type' => Sitemap::TYPE_ROOT, 'name' => 'root', 'child' => []], []);

        foreach ($methods as $method) {
            $lastSegment = $lastSegments[$method->controller] ?? 'root';
            foreach ($this->reader->getMethodAnnotations($method->reflection) as $ann) {
                switch (true) {
                    case $ann instanceof Link:
                    case $ann instanceof View:
                        $parent = $ann->parent ?? $lastSegment;
                        if ($parent !== null && isset($methods[$parent])) {
                            $parent = "{$method->controller}.$parent";
                        }
                        //todo here need to check the parent via all methods!

                        $dependencies = ($parent === null) ? [] : [$parent];

                        $this->sorter->addItem(
                            $method->route,
                            [
                                'type'    => $ann instanceof Link ? Sitemap::TYPE_LINK : Sitemap::TYPE_VIEW,
                                'name'    => $method->route,
                                'parent'  => $parent,
                                'title'   => $ann->title,
                                'options' => $ann->options + ['permission' => $method->permission],
                                'child'   => []
                            ],
                            $dependencies
                        );

                        break;
                }
            }
        }

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
