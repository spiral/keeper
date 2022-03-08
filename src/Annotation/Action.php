<?php

declare(strict_types=1);

namespace Spiral\Keeper\Annotation;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;
use Spiral\Attributes\NamedArgumentConstructor;
use Spiral\Keeper\Helper\RouteBuilder;
use Spiral\Router\Route;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"METHOD"})
 * @Attributes({
 *     @Attribute("route", required=true, type="string"),
 *     @Attribute("name", type="string"),
 *     @Attribute("verbs", required=true, type="mixed"),
 *     @Attribute("defaults", type="array"),
 *     @Attribute("group", type="string"),
 *     @Attribute("middleware", type="array")
 * })
 */
#[\Attribute(\Attribute::TARGET_METHOD), NamedArgumentConstructor]
final class Action
{
    public const DEFAULT_GROUP = 'default';

    /**
     * @var string
     */
    public $route;

    /**
     * @var string
     */
    public $name;

    /**
     * @var mixed
     */
    public $methods = Route::VERBS;

    /**
     * Default match options.
     * @var array
     */
    public $defaults = [];

    /**
     * todo Blocked until annotated routes have OCP fixed
     * Route group (set of middleware), groups can be configured using MiddlewareRegistry.
     *
     * @var string
     */
    public $group = self::DEFAULT_GROUP;

    /**
     * Route specific middleware set, if any.
     *
     * @var array
     */
    public $middleware = [];

    public function __construct(
        string $route,
        ?string $name = null,
        $methods = Route::VERBS,
        array $defaults = [],
        string $group = self::DEFAULT_GROUP,
        array $middleware = []
    ) {
        $this->route = $route;
        $this->name = $name;
        $this->methods = $methods;
        $this->defaults = $defaults;
        $this->group = $group;
        $this->middleware = $middleware;
    }

    public function toArray(string $prefix): array
    {
        return [
            'route'      => RouteBuilder::concat($prefix, $this->route),
            'name'       => $this->name ?: null,
            'verbs'      => (array)$this->methods,
            'defaults'   => $this->defaults,
            'group'      => $this->group ?: null,
            'middleware' => (array)$this->middleware,
        ];
    }
}
