<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Bootloader;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Keeper\Config\KeeperConfig;
use Spiral\Keeper\Helper\Locator;
use Spiral\Keeper\Helper\RouteBuilder;

final class AnnotatedBootloader extends Bootloader
{
    /** @var KeeperConfig */
    private $config;
    /** @var Locator */
    private $locator;

    public function __construct(KeeperConfig $config, Locator $locator)
    {
        $this->config = $config;
        $this->locator = $locator;
    }

    /**
     * @param KeeperBootloader $keeper
     * @param KeeperConfig     $config
     */
    public function boot(KeeperBootloader $keeper, KeeperConfig $config): void
    {
        AnnotationRegistry::registerLoader('class_exists');

        $annotations = iterator_to_array($this->parseAnnotations($keeper->getNamespace()));
        foreach ($annotations as $name => $controller) {
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
                $this->config->getRoutePrefix(),
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

    private function parseAnnotations(string $namespace): \Generator
    {
        /**
         * @var \ReflectionClass $class
         * @var Controller       $controller
         */
        foreach ($this->locator->locateNamespaceControllers($namespace) as $class => $controller) {
            $className = $class->getName();
            $prefix = RouteBuilder::concat($this->config->getRoutePrefix(), (string)$controller->prefix);
            yield $className => [
                'name'          => $controller->name,
                'prefix'        => $prefix,
                'defaultAction' => $controller->defaultAction ?: null,
                'class'         => $className,
                'routes'        => iterator_to_array($this->packRoutes($class, $prefix)),
            ];
        }
    }

    private function packRoutes(\ReflectionClass $class, string $prefix): \Generator
    {
        /**
         * @var \ReflectionMethod $method
         * @var Action            $action
         */
        foreach ($this->locator->locateMethodsWithAction($class) as $method => $action) {
            yield $method->getName() => $action->toArray($prefix);
        }
    }
}
