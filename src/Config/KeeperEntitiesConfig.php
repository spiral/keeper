<?php

declare(strict_types=1);

namespace Spiral\Keeper\Config;

use Psr\Http\Server\MiddlewareInterface;
use Spiral\Core\Container\Autowire;
use Spiral\Core\CoreInterceptorInterface;
use Spiral\Interceptors\InterceptorInterface;
use Spiral\Keeper\KeeperCore;
use Spiral\Keeper\Module\RouteRegistry;

/**
 * Collection of all configured entities for Keeper.
 *
 * @psalm-type StringInterceptorDefinition = class-string<InterceptorInterface>|class-string<CoreInterceptorInterface>
 * @psalm-type AutowireInterceptor = Autowire<InterceptorInterface|CoreInterceptorInterface>
 * @psalm-type Interceptor = InterceptorInterface|CoreInterceptorInterface
 *
 * @internal
 */
final class KeeperEntitiesConfig
{
    /**
     * Definitions and instances of interceptors
     * @var list<StringInterceptorDefinition|AutowireInterceptor|Interceptor>
     */
    private array $interceptors = [];

    /**
     * Definitions and instances of modules
     * @var list<array{0: object, 1: list<non-empty-string>}>
     */
    private array $modules = [];

    /**
     * Definitions and instances of controllers
     * @var array<string, string>
     */
    private array $controllers = [];

    /**
     * Definitions and instances of middlewares
     * @var list<MiddlewareInterface|string>
     */
    private array $middlewares = [];

    /**
     * @var list<\Closure(KeeperCore): void>
     */
    private array $routes = [];

    private ?KeeperCore $core = null;

    public function addInterceptor(string|Autowire|InterceptorInterface|CoreInterceptorInterface $interceptor): void
    {
        $this->core === null
            ? $this->interceptors[] = $interceptor
            : $this->core->addInterceptor($interceptor);
    }

    public function addModule(object $module, array $aliases = []): void
    {
        $this->core === null
            ? $this->modules[] = [$module, $aliases]
            : $this->core->addModule($module, $aliases);
    }

    public function addController(string $controller, string $class): void
    {
        $this->core === null
            ? $this->controllers[$controller] = $class
            : $this->core->setController($controller, $class);
    }

    public function addMiddleware(MiddlewareInterface|string $middleware): void
    {
        if ($this->core === null) {
            $this->middlewares[] = $middleware;
            return;
        }

        /** @var RouteRegistry $registry */
        $registry = $this->core->getModule(RouteRegistry::class);
        $registry->addMiddleware($middleware);
    }

    /**
     * @param \Closure(KeeperCore): void $closure
     */
    public function addRouteRegistrar(\Closure $closure): void
    {
        $this->core === null
            ? $this->routes[] = $closure
            : $closure($this->core);
    }

    /**
     * @return array<string, string>
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    /**
     * @return list<array{0: object, 1: list<non-empty-string>}>
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * @return list<StringInterceptorDefinition|AutowireInterceptor|Interceptor>
     */
    public function getInterceptors(): array
    {
        return $this->interceptors;
    }

    /**
     * @return list<MiddlewareInterface|string>
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @return list<\Closure(KeeperCore): void>
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function setCore(KeeperCore $core): void
    {
        $this->core = $core;

        $this->apply();

        $this->modules = [];
        $this->controllers = [];
        $this->middlewares = [];
        $this->interceptors = [];
    }

    private function apply(): void
    {
        // Apply modules
        foreach ($this->modules as [$module, $aliases]) {
            $this->core->addModule($module, $aliases);
        }

        // Apply controllers
        foreach ($this->controllers as $controller => $class) {
            $this->core->setController($controller, $class);
        }

        // Apply interceptors
        foreach ($this->interceptors as $interceptor) {
            $this->core->addInterceptor($interceptor);
        }

        // Apply middlewares
        if ($this->middlewares !== []) {
            $registry = $this->core->getModule(RouteRegistry::class);
            foreach ($this->middlewares as $middleware) {
                $registry->addMiddleware($middleware);
            }
        }
    }
}
