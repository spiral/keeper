<?php

declare(strict_types=1);

namespace Spiral\Keeper\Module\Sitemap;

use Spiral\Domain\Annotation\Guarded;
use Spiral\Domain\Annotation\GuardNamespace;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Sitemap\Link;

final class Method
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
        ?GuardNamespace $guardNamespace = null,
        ?Guarded $guarded = null,
        ?Link $link = null
    ): self {
        $method = $reflection->getName();

        $permission = array_filter(
            [
                $guardNamespace && $guardNamespace->namespace ? $guardNamespace->namespace : "$namespace.$controller",
                self::permission($link, $guarded, $method)
            ],
            static function ($chunk): bool {
                return (bool)$chunk;
            }
        );

        return new self(
            $reflection,
            $action->name ?: "$controller.$method",
            $controller,
            implode('.', $permission)
        );
    }

    public function name(): string
    {
        return "{$this->controller}.{$this->name}";
    }

    private static function permission(?Link $link, ?Guarded $guarded, string $method): string
    {
        if ($link && $link->permission) {
            return $link->permission;
        }

        if ($guarded && $guarded->permission) {
            return $guarded->permission;
        }

        return $method;
    }
}
