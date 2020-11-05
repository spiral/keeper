<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Module;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Spiral\Keeper\Config\KeeperConfig;
use Spiral\Router\Exception\RouterException;
use Spiral\Router\Exception\UndefinedRouteException;
use Spiral\Router\Route;
use Spiral\Router\RouteInterface;
use Spiral\Router\Router;
use Spiral\Router\RouterInterface;
use Spiral\Router\UriHandler;

final class RouteRegistry
{
    /** @var KeeperConfig */
    private $config;

    /** @var RouterInterface */
    private $router;

    /** @var MiddlewareInterface[]|string[] */
    private $middleware;

    /** @var RouterInterface */
    private $appRouter;

    /**
     * @param ContainerInterface $container
     * @param KeeperConfig       $config
     * @param RouterInterface    $appRouter
     */
    public function __construct(ContainerInterface $container, KeeperConfig $config, RouterInterface $appRouter)
    {
        $this->config = $config;
        $this->middleware = $this->config->getMiddleware();

        $this->router = new Router(
            $config->getRoutePrefix(),
            $container->get(UriHandler::class),
            $container
        );
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
        $this->router->setRoute($name, $route instanceof Route ? $this->configureRoute($route) : $route);
    }

    /**
     * Provides the ability to inject templated args in a form or {id} or {{id}}.
     *
     * @param string $namespace
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
                $this->appRouter->uri($this->wrapRouterName($namespace, $route), $vars)->__toString(),
                $restore
            );
        } catch (UndefinedRouteException $e) {
            throw new RouterException("No such route {$route}", $e->getCode(), $e);
        }
    }

    /**
     * @param string $namespace
     * @return void
     */
    public function hydrate(string $namespace): void
    {
        foreach ($this->router->getRoutes() as $name => $route) {
            $this->appRouter->setRoute($this->wrapRouterName($namespace, $name), $route);
        }
    }

    private function wrapRouterName(string $namespace, string $name): string
    {
        return "$namespace\[$name\]";
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
