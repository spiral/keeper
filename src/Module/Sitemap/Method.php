<?php

declare(strict_types=1);

namespace Spiral\Keeper\Module\Sitemap;

use Spiral\Domain\Annotation\Guarded;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Helper\RouteBuilder;

class Method
{
    /** @var \ReflectionMethod */
    public $reflection;
    /** @var string */
    public $name;
    /** @var string */
    public $controller;
    /** @var string */
    public $route;
    /** @var string|null */
    public $permission;

    public function __construct(
        \ReflectionMethod $reflection,
        string $route,
        string $controller,
        ?string $permission = null
    ) {
        $this->reflection = $reflection;
        $this->name = $reflection->getName();
        $this->controller = $controller;
        $this->route = $route;
        $this->permission = $permission;
    }

    public static function create(
        string $namespace,
        string $controller,
        \ReflectionMethod $reflection,
        Action $action,
        ?string $permission = null
    ): self {
        $method = $reflection->getName();
        return new self(
            $reflection,
            RouteBuilder::routeName($namespace, $action->name ?: "$controller.$method"),
            $controller,
            $permission instanceof Guarded && $permission->permission
                ? $permission->permission
                : "$controller.$method"
        );
    }
}
