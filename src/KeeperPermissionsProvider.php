<?php

declare(strict_types=1);

namespace Spiral\Keeper;

use Doctrine\Common\Annotations\AnnotationReader;
use Spiral\Core\Exception\ControllerException;
use Spiral\Domain\Annotation\Guarded;
use Spiral\Domain\PermissionsProviderInterface;

class KeeperPermissionsProvider implements PermissionsProviderInterface
{
    private const FAILURE_MAP = [
        'unauthorized' => ControllerException::UNAUTHORIZED,
        'badAction'    => ControllerException::BAD_ACTION,
        'notFound'     => ControllerException::NOT_FOUND,
        'error'        => ControllerException::ERROR,
    ];

    /** @var array */
    private $cache = [];

    /** @var array */
    private $aliases = [];

    /** @var string */
    private $namespace;

    /** @var PermissionsProviderInterface */
    private $permissions;

    /** @var AnnotationReader */
    private $reader;

    public function __construct(string $namespace, PermissionsProviderInterface $permissions, AnnotationReader $reader)
    {
        $this->namespace = $namespace;
        $this->permissions = $permissions;
        $this->reader = $reader;
    }

    public function addAlias(string $controller, string $alias): void
    {
        $this->aliases[$controller] = $alias;
    }

    public function getPermission(string $controller, string $action): ?array
    {
        $permission = $this->permissions->getPermission($controller, $action);
        if ($permission !== null) {
            return $permission;
        }

        $key = sprintf('%s:%s', $controller, $action);
        if (!array_key_exists($key, $this->cache)) {
            $guarded = $this->readGuardedAnnotation($controller, $action);
            $this->cache[$key] = [
                "{$this->namespace}.{$this->getControllerAlias($controller)}.$action",
                $this->mapFailureException($guarded),
                $this->getErrorMessage($guarded, $action)
            ];
        }

        return $this->cache[$key];
    }

    private function getControllerAlias(string $controller): string
    {
        if (!isset($this->aliases[$controller])) {
            throw new ControllerException(
                sprintf('No such controller `%s`', $controller),
                ControllerException::NOT_FOUND
            );
        }

        return $this->aliases[$controller];
    }

    private function readGuardedAnnotation(string $controller, string $action): ?Guarded
    {
        try {
            $method = new \ReflectionMethod($controller, $action);
            $guarded = $this->reader->getMethodAnnotation($method, Guarded::class);
            return $guarded instanceof Guarded ? $guarded : null;
        } catch (\ReflectionException $e) {
        }

        return null;
    }

    private function mapFailureException(?Guarded $guarded): int
    {
        return $guarded instanceof Guarded && self::FAILURE_MAP[$guarded->else]
            ? self::FAILURE_MAP[$guarded->else]
            : ControllerException::FORBIDDEN;
    }

    private function getErrorMessage(?Guarded $guarded, string $action): string
    {
        if ($guarded instanceof Guarded) {
            return $guarded->errorMessage ?: sprintf(
                'Unauthorized access `%s`',
                $guarded->permission ?: $action
            );
        }

        return "Unauthorized access `{$action}`";
    }
}
