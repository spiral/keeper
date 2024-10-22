<?php

declare(strict_types=1);

namespace Spiral\Keeper\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Bootloader\Attributes\AttributesBootloader;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Keeper\Config\KeeperConfig;
use Spiral\Keeper\Helper\Locator;
use Spiral\Keeper\Helper\RouteBuilder;

final class AnnotatedBootloader extends Bootloader implements KeeperBootloaderInterface
{
    protected const DEPENDENCIES = [
        AttributesBootloader::class,
    ];

    public function __construct(
        private readonly KeeperConfig $config,
        private readonly Locator $locator,
    ) {}

    public function boot(KeeperBootloader $keeper): void
    {
        $annotations = \iterator_to_array($this->parseAnnotations($keeper->getNamespace()));
        foreach ($annotations as $controller) {
            $keeper->addController($controller['name'], $controller['class']);

            if ($controller['defaultAction'] && isset($controller['routes'][$controller['defaultAction']])) {
                $route = $controller['routes'][$controller['defaultAction']];
                $keeper->addRoute(
                    (string) $controller['prefix'],
                    $controller['name'],
                    $controller['defaultAction'],
                    $route['verbs'],
                    $controller['name'],
                    $route['defaults'],
                    $route['group'],
                    $route['middleware'],
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
                    $route['middleware'],
                );
            }
        }

        $defaults = $this->config->getDefaults();
        if (!isset($defaults['controller'], $annotations[$defaults['controller']])) {
            return;
        }

        $controller = $annotations[$defaults['controller']];
        $action = $this->getDefaultControllerAction($controller);

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
                $route['middleware'],
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
            $prefix = RouteBuilder::concat($this->config->getRoutePrefix(), (string) $controller->prefix);
            yield $className => [
                'name'          => $controller->name,
                'prefix'        => $prefix,
                'defaultAction' => $controller->defaultAction ?: null,
                'class'         => $className,
                'routes'        => \iterator_to_array($this->packRoutes($class, $prefix)),
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

    private function getDefaultControllerAction(array $controller): ?string
    {
        $defaults = $this->config->getDefaults();
        if (isset($defaults['action'], $controller['routes'][$defaults['action']])) {
            return $defaults['action'];
        }

        if (isset($controller['routes'][$controller['defaultAction'] ?: 'index'])) {
            return $controller['defaultAction'] ?: 'index';
        }

        return null;
    }
}
