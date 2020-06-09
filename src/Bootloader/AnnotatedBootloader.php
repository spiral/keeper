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
use Spiral\Annotations\AnnotationLocator;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Helpers\GraphSorter;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Keeper\Annotation\Sitemap\Group;
use Spiral\Keeper\Annotation\Sitemap\Link;
use Spiral\Keeper\Annotation\Sitemap\Segment;
use Spiral\Keeper\Annotation\Sitemap\View;
use Spiral\Keeper\Module\Sitemap;

final class AnnotatedBootloader extends Bootloader
{
    /**
     * @param KeeperBootloader  $keeper
     * @param AnnotationLocator $locator
     * @param Sitemap           $sitemap
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function boot(KeeperBootloader $keeper, AnnotationLocator $locator, Sitemap $sitemap): void
    {
        AnnotationRegistry::registerLoader('class_exists');

        $annotations = $this->parseAnnotations($keeper->getNamespace(), $locator);
        foreach ($annotations as $controller) {
            $keeper->addController($controller['name'], $controller['class']);

            foreach ($controller['routes'] as $method => $route) {
                $keeper->addRoute($route['route'], $controller['name'], $method, $route['verbs']);
            }

            foreach ($controller['sitemap'] as $item) {
                $this->setSitemap($sitemap, $item);
            }
        }
    }

    /**
     * @param Sitemap $sitemap
     * @param array   $item
     */
    private function setSitemap(Sitemap $sitemap, array $item): void
    {
        $node = $sitemap->{$item['type']}($item['name'], $item['title'], $item['options']);

        foreach ($item['child'] as $child) {
            $this->setSitemap($node, $child);
        }
    }

    /**
     * @param string            $namespace
     * @param AnnotationLocator $locator
     * @return array
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    private function parseAnnotations(string $namespace, AnnotationLocator $locator): array
    {
        $reader = new AnnotationReader();

        $annotations = [];
        foreach ($locator->findClasses(Controller::class) as $match) {
            if ($match->getAnnotation()->namespace !== $namespace) {
                continue;
            }

            $controller = [
                'name'    => $match->getAnnotation()->name,
                'class'   => $match->getClass()->getName(),
                'routes'  => [],
                'sitemap' => []
            ];

            foreach ($match->getClass()->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $action = $reader->getMethodAnnotation($method, Action::class);
                if ($action === null) {
                    continue;
                }

                $path = $match->getAnnotation()->name . '/';
                if ($match->getAnnotation()->prefix !== null) {
                    $path = $match->getAnnotation()->prefix;
                }

                $route = str_replace('//', '/', $path . $action->route);

                $controller['routes'][$method->getName()] = [
                    'route' => $route,
                    'verbs' => (array) $action->methods,
                ];
            }

            $controller['sitemap'] = $this->buildSitemap(
                $match->getClass(),
                $reader,
                $namespace,
                $controller['name'],
                array_keys($controller['routes'])
            );
            $annotations[] = $controller;
        }

        return $annotations;
    }

    /**
     * @param \ReflectionClass $class
     * @param AnnotationReader $reader
     * @param string           $namespace
     * @param string           $controller
     * @param array            $methods
     * @return array
     */
    private function buildSitemap(
        \ReflectionClass $class,
        AnnotationReader $reader,
        string $namespace,
        string $controller,
        array $methods
    ): array {
        $gs = new GraphSorter();
        $gs->addItem('root', ['type' => Sitemap::TYPE_ROOT, 'name' => 'root', 'child' => []], []);

        $lastSegment = 'root';
        foreach ($reader->getClassAnnotations($class) as $ann) {
            switch (true) {
                case $ann instanceof Segment:
                case $ann instanceof Group:
                    $lastSegment = $ann->name;
                    $gs->addItem(
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

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (!in_array($method->getName(), $methods, true)) {
                continue;
            }

            foreach ($reader->getMethodAnnotations($method) as $ann) {
                // name and route is the same thing
                $name = sprintf('%s.%s', $controller, $method->getName());

                switch (true) {
                    case $ann instanceof Link:
                    case $ann instanceof View:
                        $parent = $ann->parent ?? $lastSegment;
                        if ($parent !== null && in_array($parent, $methods, true)) {
                            $parent = sprintf('%s.%s', $controller, $parent);
                        }

                        $dependencies = ($parent === null) ? [] : [$parent];

                        $gs->addItem(
                            $name,
                            [
                                'type'    => $ann instanceof Link ? Sitemap::TYPE_LINK : Sitemap::TYPE_VIEW,
                                'name'    => $name,
                                'parent'  => $parent,
                                'title'   => $ann->title,
                                'options' => $ann->options + ['permission' => sprintf('%s.%s', $namespace, $name)],
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
        foreach ($gs->sort() as $item) {
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
}
