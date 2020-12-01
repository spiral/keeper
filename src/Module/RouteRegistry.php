<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Module;

use Psr\Http\Server\MiddlewareInterface;
use Spiral\Keeper\Config\KeeperConfig;
use Spiral\Keeper\Helper\RouteBuilder;
use Spiral\Router\Route;
use Spiral\Router\RouteInterface;
use Spiral\Router\RouterInterface;

final class RouteRegistry
{
    /** @var KeeperConfig */
    private $config;

    /** @var MiddlewareInterface[]|string[] */
    private $middleware;

    /** @var RouterInterface */
    private $appRouter;

    private $names = [];

    /**
     * @param KeeperConfig    $config
     * @param RouterInterface $appRouter
     */
    public function __construct(KeeperConfig $config, RouterInterface $appRouter)
    {
        $this->config = $config;
        $this->middleware = $this->config->getMiddleware();
        $this->appRouter = $appRouter;
    }

    /**
     * @param string|MiddlewareInterface $middleware
     */
    public function addMiddleware($middleware): void
    {
        if (!in_array($middleware, $this->middleware, true)) {
            $this->middleware[] = $middleware;

            foreach ($this->appRouter->getRoutes() as $name => $route) {
                if (!isset($this->names[$name]) || !$route instanceof Route) {
                    continue;
                }

                $this->appRouter->setRoute($name, $route->withMiddleware($middleware));
            }
        }
    }

    /**
     * @param string         $name
     * @param RouteInterface $route
     */
    public function setRoute(string $name, RouteInterface $route): void
    {
        $this->names[RouteBuilder::routeName($this->config->getNamespace(), $name)] = true;
        $this->appRouter->setRoute(
            RouteBuilder::routeName($this->config->getNamespace(), $name),
            $route instanceof Route ? $this->configureRoute($route) : $route
        );
    }

    /**
     * Provides the ability to inject templated args in a form or {id} or {{id}}.
     *
     * @param string            $namespace
     * @param string|array|null $route
     * @param array             $parameters
     * @return string
     * @deprecated use RouteBuilder::uri()
     */
    public function uri(string $namespace, $route = null, array $parameters = []): string
    {
        [
            'namespace'  => $namespace,
            'route'      => $route,
            'parameters' => $parameters
        ] = $this->handleLegacyUriParams($namespace, $route, $parameters);

        $builder = new RouteBuilder($this->appRouter);
        return $builder->uri($namespace, $route, $parameters);
    }

    private function handleLegacyUriParams(string $namespace, $route = null, array $parameters = []): array
    {
        if (!empty($parameters) && !is_string($route)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Route name should be a string in case of parameters are provided, %s given',
                    gettype($route)
                )
            );
        }

        $route = $route ?: null;
        if (empty($route) && empty($parameters)) {
            [$namespace, $route] = ['keeper', $namespace];
        } elseif (is_array($route)) {
            [$namespace, $route, $parameters] = ['keeper', $namespace, $route];
        }

        return compact('namespace', 'route', 'parameters');
    }

    /**
     * Assign middlewares to a given route.
     *
     * @param Route $route
     * @return RouteInterface
     */
    private function configureRoute(Route $route): RouteInterface
    {
        $defaults = array_merge($this->config->getDefaults(), $route->getDefaults());
        return $route->withMiddleware(...$this->middleware)->withDefaults($defaults);
    }
}
