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
use Spiral\Router\Exception\RouterException;
use Spiral\Router\Exception\UndefinedRouteException;
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
        $this->middleware[] = $middleware;
    }

    /**
     * @param string         $name
     * @param RouteInterface $route
     */
    public function setRoute(string $name, RouteInterface $route): void
    {
        $this->appRouter->setRoute(
            $this->wrapWithNamespace($name),
            $route instanceof Route ? $this->configureRoute($route) : $route
        );
    }

    /**
     * Provides the ability to inject templated args in a form or {id} or {{id}}.
     *
     * @param string $route
     * @param array  $parameters
     * @return string
     */
    public function uri(string $namespace, string $route, array $parameters = []): string
    {
        $vars = [];
        $restore = [];
        foreach ($parameters as $key => $value) {
            if (is_string($value) && preg_match('/{.*}/', $value)) {
                $restore[sprintf('__%s__', $key)] = $value;
                $value = sprintf('__%s__', $key);
            }

            $vars[$key] = $value;
        }

        try {
            return strtr(
                $this->appRouter->uri($this->wrapWithNamespace($route), $vars)->__toString(),
                $restore
            );
        } catch (UndefinedRouteException $e) {
            throw new RouterException("No such route {$route}", $e->getCode(), $e);
        }
    }

    private function wrapWithNamespace(string $name): string
    {
        return $name ? "{$this->config->getNamespace()}[$name]" : $this->config->getNamespace();
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
