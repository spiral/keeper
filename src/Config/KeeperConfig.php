<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Config;

/**
 * Scope specific config for keeper.
 */
final class KeeperConfig
{
    /**
     * @param non-empty-string $namespace
     */
    public function __construct(
        private readonly string $namespace,
        private readonly array $config,
    ) {}

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getDefaults(): array
    {
        return $this->config['routeDefaults'] ?? [];
    }

    public function getRoutePrefix(): string
    {
        return $this->config['routePrefix'];
    }

    public function getMiddleware(): array
    {
        return $this->config['middleware'];
    }

    public function getModuleBootloaders(): array
    {
        return $this->config['bootload'] ?? $this->config['modules'];
    }

    public function getInterceptors(): array
    {
        return $this->config['interceptors'];
    }

    public function getLoginView(): string
    {
        return $this->config['loginView'];
    }
}
