<?php

declare(strict_types=1);

namespace Spiral\Keeper\Module\Sitemap;

use Spiral\Domain\Annotation\Guarded;
use Spiral\Keeper\Annotation\Action;

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
        ?Guarded $guarded = null
    ): self {
        $method = $reflection->getName();
        $permission = $guarded instanceof Guarded && $guarded->permission
            ? $guarded->permission
            : "$controller.$method";
        return new self(
            $reflection,
            $action->name ?: "$controller.$method",
            $controller,
            "$namespace.$permission"
        );
    }

    public function name(): string
    {
        return "{$this->controller}.{$this->name}";
    }
}
