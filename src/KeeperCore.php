<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper;

use Spiral\Core\Container\InjectorInterface;
use Spiral\Core\CoreInterceptorInterface;
use Spiral\Core\CoreInterface;
use Spiral\Core\Exception\ControllerException;
use Spiral\Core\InterceptableCore;
use Spiral\Core\ScopeInterface;
use Spiral\Keeper\Exception\KeeperException;
use Spiral\Security\GuardInterface;

final class KeeperCore implements CoreInterface, InjectorInterface
{
    /** @var string */
    private $namespace;

    /** @var array */
    private $controllers = [];

    /** @var array */
    private $aliases = [];

    /** @var ScopeInterface */
    private $scope;

    /** @var array */
    private $modules = [];

    /** @var GuardInterface */
    private $guard;

    /** @var InterceptableCore */
    private $invoker;

    /**
     * @param ScopeInterface $scope
     * @param CoreInterface  $core
     * @param GuardInterface $guard
     * @param string         $namespace
     */
    public function __construct(
        ScopeInterface $scope,
        CoreInterface $core,
        GuardInterface $guard,
        string $namespace
    ) {
        $this->invoker = new InterceptableCore($core);
        $this->scope = $scope;
        $this->guard = $guard;
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @param string $name
     * @param string $target
     */
    public function setController(string $name, string $target): void
    {
        $this->controllers[$name] = $target;
        $this->aliases[$target] = $name;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getController(string $name): string
    {
        if (!isset($this->controllers[$name])) {
            throw new ControllerException(
                sprintf('No such controller `%s`', $name),
                ControllerException::NOT_FOUND
            );
        }

        return $this->controllers[$name];
    }

    /**
     * Adds domain core interceptor.
     *
     * @param CoreInterceptorInterface $interceptor
     */
    public function addInterceptor(CoreInterceptorInterface $interceptor): void
    {
        $this->invoker->addInterceptor($interceptor);
    }

    /**
     * @param object $module
     * @param array  $aliases
     */
    public function addModule(object $module, array $aliases): void
    {
        foreach ($aliases as $alias) {
            $this->modules[$alias] = $module;
        }
    }

    /**
     * @param string $name
     * @return object
     */
    public function getModule(string $name): object
    {
        if (!isset($this->modules[$name])) {
            throw new KeeperException("No such module `{$name}`");
        }

        return $this->modules[$name];
    }

    /**
     * @param \ReflectionClass $class
     * @param string|null      $context
     * @return object|null
     */
    public function createInjection(\ReflectionClass $class, string $context = null): ?object
    {
        return $this->getModule($class->getName());
    }

    /**
     * @param string $controller
     * @param string $action
     * @param array  $parameters
     * @return mixed
     * @throws \Throwable
     */
    public function callAction(string $controller, string $action, array $parameters = [])
    {
        $alias = $this->getControllerAlias($controller);
        if (!$this->guard->allows("{$this->namespace}.$alias.$action", $parameters)) {
            throw new ControllerException(
                "Unable to call `{$alias}`->`{$action}`, forbidden",
                ControllerException::FORBIDDEN
            );
        }

        return $this->scope->runScope(
            [
                self::class          => $this,
                CoreInterface::class => $this
            ],
            function () use ($controller, $action, $parameters) {
                return $this->invoker->callAction($controller, $action, $parameters);
            }
        );
    }

    private function getControllerAlias(string $controller)
    {
        if (!isset($this->aliases[$controller])) {
            throw new ControllerException(
                sprintf('No such controller `%s`', $controller),
                ControllerException::NOT_FOUND
            );
        }

        return $this->aliases[$controller];
    }
}
