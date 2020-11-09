<?php

declare(strict_types=1);

namespace Spiral\Keeper\Helper;

class RouteBuilder
{
    public static function routeName(string $namespace, string $name = null): string
    {
        return $name ? "{$namespace}[$name]" : $namespace;
    }
}
