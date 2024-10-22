<?php

declare(strict_types=1);

namespace Spiral\Keeper\Helper;

use Spiral\Router\Exception\RouterException;
use Spiral\Router\Exception\UndefinedRouteException;
use Spiral\Router\RouterInterface;

class RouteBuilder
{
    /** @var RouterInterface */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    final public static function routeName(string $namespace, string $name = null): string
    {
        return $name ? "{$namespace}[$name]" : $namespace;
    }

    public static function concat(string ...$chunks): string
    {
        return \preg_replace('/\/+/', '/', \implode('', $chunks));
    }

    public function uri(string $namespace, string $route, array $parameters = []): string
    {
        $vars = [];
        $restore = [];
        foreach ($parameters as $key => $value) {
            if (\is_string($value) && \preg_match('/{.*}/', $value)) {
                $restore[\sprintf('__%s__', $key)] = $value;
                $value = \sprintf('__%s__', $key);
            }

            $vars[$key] = $value;
        }

        try {
            return \strtr(
                $this->router->uri(static::routeName($namespace, $route), $vars)->__toString(),
                $restore,
            );
        } catch (UndefinedRouteException $e) {
            throw new RouterException("No such route {$route}", $e->getCode(), $e);
        }
    }
}
