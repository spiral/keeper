<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Annotation;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Spiral\Keeper\Helper\RouteBuilder;
use Spiral\Router\Route;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
final class Action
{
    public const DEFAULT_GROUP = 'default';

    /**
     * @Attribute(name="route", type="string", required=true)
     * @var string
     */
    public $route;

    /**
     * @Attribute(name="name", type="string")
     * @var string
     */
    public $name;

    /**
     * @Attribute(name="verbs", type="mixed", required=true)
     * @var mixed
     */
    public $methods = Route::VERBS;

    /**
     * Default match options.
     *
     * @Attribute(name="defaults", type="array")
     * @var array
     */
    public $defaults = [];

    /**
     * todo Blocked until annotated routes have OCP fixed
     * Route group (set of middleware), groups can be configured using MiddlewareRegistry.
     *
     * @Attribute(name="group", type="string")
     * @var string
     */
    public $group = self::DEFAULT_GROUP;

    /**
     * Route specific middleware set, if any.
     *
     * @Attribute(name="middleware", type="array")
     * @var array
     */
    public $middleware = [];

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
