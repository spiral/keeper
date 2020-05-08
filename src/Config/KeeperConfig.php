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
    /** @var array */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getRoutePrefix(): string
    {
        return $this->config['routePrefix'];
    }

    /**
     * @return string
     */
    public function getRoutePattern(): string
    {
        return $this->config['routePattern'];
    }

    /**
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->config['middleware'];
    }

    /**
     * @return array
     */
    public function getModuleBootloaders(): array
    {
        return $this->config['bootload'] ?? $this->config['modules'];
    }

    /**
     * @return array
     */
    public function getInterceptors(): array
    {
        return $this->config['interceptors'];
    }

    /**
     * @return string
     */
    public function getLoginView(): string
    {
        return $this->config['loginView'];
    }
}