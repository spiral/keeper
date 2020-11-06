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
use Spiral\Keeper\Config\KeeperConfig;
use Spiral\Keeper\Module\Sitemap;

final class AnnotatedBootloader extends Bootloader
{
    /** @var AnnotationReader */
    private $reader;

    /** @var AnnotationLocator */
    private $locator;

    /** @var KeeperConfig */
    private $config;

    /**
     * @param AnnotationReader  $reader
     * @param AnnotationLocator $locator
     * @param KeeperConfig      $config
     */
    public function __construct(AnnotationReader $reader, AnnotationLocator $locator, KeeperConfig $config)
    {
        $this->reader = $reader;
        $this->locator = $locator;
        $this->config = $config;
    }

    /**
     * @param KeeperBootloader $keeper
     * @param Sitemap          $sitemap
     * @param KeeperConfig     $config
     */
    public function boot(KeeperBootloader $keeper, Sitemap $sitemap, KeeperConfig $config): void
    {
        AnnotationRegistry::registerLoader('class_exists');

        $annotations = $this->parseAnnotations($keeper->getNamespace());
        foreach ($annotations as $controller) {
            $keeper->addController($controller['name'], $controller['class']);

            if ($controller['defaultAction'] && isset($controller['routes'][$controller['defaultAction']])) {
                $route = $controller['routes'][$controller['defaultAction']];
                $keeper->addRoute(
                    (string)$controller['prefix'],
                    $controller['name'],
                    $controller['defaultAction'],
                    $route['verbs'],
                    $controller['name'],
                    $route['defaults'],
                    $route['group'],
                    $route['middleware']
                );
            }

            foreach ($controller['routes'] as $method => $route) {
                $keeper->addRoute(
                    $route['route'],
                    $controller['name'],
                    $method,
                    $route['verbs'],
                    $route['name'],
                    $route['defaults'],
                    $route['group'],
                    $route['middleware']
                );
            }

            foreach ($controller['sitemap'] as $item) {
                $this->setSitemap($sitemap, $item);
            }
        }

        if (!isset($config->getDefaults()['controller'], $annotations[$config->getDefaults()['controller']])) {
            return;
        }

        $controller = $annotations[$config->getDefaults()['controller']];
        $action = null;
        if (isset($config->getDefaults()['action'], $controller['routes'][$config->getDefaults()['action']])) {
            $action = $config->getDefaults()['action'];
        } elseif (isset($controller['routes'][$controller['defaultAction'] ?: 'index'])) {
            $action = $controller['defaultAction'] ?: 'index';
        }

        if ($action !== null) {
            $route = $controller['routes'][$action];
            $keeper->addRoute(
                '',
                $controller['name'],
                $action,
                $route['verbs'],
                '',
                $route['defaults'],
                $route['group'],
                $route['middleware']
            );
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
     * @param string $namespace
     * @return array
     */
    private function parseAnnotations(string $namespace): array
    {
        $annotations = [];
        foreach ($this->locator->findClasses(Controller::class) as $match) {
            /** @var Controller $controller */
            $controller = $match->getAnnotation();
            if ($controller->namespace !== $namespace) {
                continue;
            }

            $prefix = $this->config->getRoutePrefix() . $controller->prefix;
            $annotation = [
                'name'          => $controller->name,
                'prefix'        => $prefix,
                'defaultAction' => $controller->defaultAction ?: null,
                'class'         => $match->getClass()->getName(),
                'routes'        => [],
                'sitemap'       => []
            ];

            foreach ($match->getClass()->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $action = $this->reader->getMethodAnnotation($method, Action::class);
                if (!$action instanceof Action) {
                    continue;
                }

                $annotation['routes'][$method->getName()] = $action->toArray($prefix);
            }

            $annotation['sitemap'] = $this->buildSitemap(
                $match->getClass(),
                $namespace,
                $controller->name,
                array_keys($annotation['routes'])
            );
            $annotations[$match->getClass()->getName()] = $annotation;
        }

        return $annotations;
    }

    /**
     * @param \ReflectionClass $class
     * @param string           $namespace
     * @param string           $controller
     * @param array            $methods
     * @return array
     */
    private function buildSitemap(
        \ReflectionClass $class,
        string $namespace,
        string $controller,
        array $methods
    ): array {
        $gs = new GraphSorter();
        $gs->addItem('root', ['type' => Sitemap::TYPE_ROOT, 'name' => 'root', 'child' => []], []);

        $lastSegment = 'root';
        foreach ($this->reader->getClassAnnotations($class) as $ann) {
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

            foreach ($this->reader->getMethodAnnotations($method) as $ann) {
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
