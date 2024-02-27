<?php

declare(strict_types=1);

namespace Spiral\Keeper\Bootloader;

use Spiral\Attributes\ReaderInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Bootloader\Attributes\AttributesBootloader;
use Spiral\Domain\Annotation\Guarded;
use Spiral\Domain\Annotation\GuardNamespace;
use Spiral\Helpers\GraphSorter;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Keeper\Annotation\Sitemap\Group;
use Spiral\Keeper\Annotation\Sitemap\Link;
use Spiral\Keeper\Annotation\Sitemap\Segment;
use Spiral\Keeper\Annotation\Sitemap\View;
use Spiral\Keeper\Helper\Locator;
use Spiral\Keeper\Module\Sitemap;

class SitemapBootloader extends Bootloader implements KeeperBootloaderInterface
{
    protected const ROOT = Sitemap::TYPE_ROOT;

    protected const DEPENDENCIES = [
        AttributesBootloader::class,
    ];

    /** @var ReaderInterface */
    private $reader;
    /** @var Locator */
    private $locator;
    /** @var GraphSorter */
    private $sorter;
    /** @var array */
    private $cache = [];

    public function __construct(ReaderInterface $reader, Locator $locator)
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
        $this->addItem(
            $node->getOption('type'),
            $node->getName(),
            [
                'type'    => $node->getOption('type'),
                'name'    => $node->getName(),
                'parent'  => $parent ? $parent->getName() : static::ROOT,
                'title'   => $node->getOption('title'),
                'options' => $node->getOptions(),
                'child'   => []
            ],
            [$parent ? $parent->getName() : static::ROOT]
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
        /** @psalm-suppress UnusedForeachValue, NoValue */
        foreach ($this->reader->getClassMetadata($class) as $ann) {
            switch (true) {
                case $ann instanceof Segment:
                case $ann instanceof Group:
                    $lastSegment = $ann->name;
                    $this->addItem(
                        $ann instanceof Segment ? Sitemap::TYPE_SEGMENT : Sitemap::TYPE_GROUP,
                        $ann->name,
                        [
                            'type'    => $ann instanceof Segment ? Sitemap::TYPE_SEGMENT : Sitemap::TYPE_GROUP,
                            'name'    => $ann->name,
                            'parent'  => $ann->parent,
                            'title'   => $ann->title,
                            'options' => $ann->options + ['position' => $ann->position],
                            'child'   => []
                        ],
                        $ann->parent ? [$ann->parent] : []
                    );
            }
        }
        return $lastSegment;
    }

    private function packMethods(string $namespace, \ReflectionClass $class, string $controller): iterable
    {
        /**
         * @var \ReflectionMethod   $method
         * @var Action              $action
         * @var Guarded|null        $guarded
         * @var Link|null           $link
         */
        $guardNamespace = $this->reader->firstClassMetadata($class, GuardNamespace::class);
        foreach ($this->locator->locateMethodsWithAction($class) as $method => $action) {
            $guarded = $this->reader->firstFunctionMetadata($method, Guarded::class);
            $link = $this->reader->firstFunctionMetadata($method, Link::class);
            $method = Sitemap\Method::create(
                $namespace,
                $controller,
                $method,
                $action,
                $guardNamespace,
                $guarded,
                $link
            );
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
            /** @psalm-suppress NoValue */
            foreach ($this->reader->getFunctionMetadata($method->reflection) as $ann) {
                switch (true) {
                    case $ann instanceof Link:
                    case $ann instanceof View:
                        $parent = $this->getParent($ann, $method, $methods, $sitemapElements, $lastSegment);

                        $this->addItem(
                            $ann instanceof Link ? Sitemap::TYPE_LINK : Sitemap::TYPE_VIEW,
                            $method->name(),
                            [
                                'type'    => $ann instanceof Link ? Sitemap::TYPE_LINK : Sitemap::TYPE_VIEW,
                                'name'    => $method->name(),
                                'parent'  => $parent,
                                'title'   => $ann->title,
                                'options' => $ann->getOptions($method),
                                'child'   => []
                            ],
                            $parent ? [$parent] : []
                        );

                        break;
                }
            }
        }
    }

    /**
     * @param Link|View      $ann
     * @param Sitemap\Method $method
     * @param array          $methods
     * @param array          $sitemapElements
     * @param string         $lastSegment
     * @return string
     */
    private function getParent(
        $ann,
        Sitemap\Method $method,
        array $methods,
        array $sitemapElements,
        string $lastSegment
    ): string {
        $parent = null;
        if ($ann->hasAbsoluteParent()) {
            $parent = $ann->parent;
        } elseif ($ann->hasRelativeParent()) {
            $parent = "{$method->controller}.{$ann->parent}";
        }

        if ($parent && (isset($methods[$parent]) || in_array($parent, $sitemapElements, true))) {
            return $parent;
        }

        return $lastSegment;
    }

    private function addItem(string $type, string $name, array $item, array $dependencies): void
    {
        $key = "$type:$name";
        if (!isset($this->cache[$key])) {
            $this->cache[$key] = true;
            $this->sorter->addItem($name, $item, $dependencies);
        }
    }

    private function sort()
    {
        // wood working
        $root = null;
        $_ = [];
        foreach ($this->sorter->sort() as $item) {
            $_[$item['name']] = &$item;
            if ($root === null) {
                $root = &$item;
                unset($item);
                continue;
            }

            $_[$item['parent']]['child'][] = &$item;
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
