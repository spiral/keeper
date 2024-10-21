<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper;

use Psr\Container\ContainerInterface;
use Spiral\Core\Attribute\Proxy;
use Spiral\Core\BinderInterface;
use Spiral\Core\CompatiblePipelineBuilder;
use Spiral\Core\Container\Autowire;
use Spiral\Core\Container\InjectorInterface;
use Spiral\Core\ContainerScope;
use Spiral\Core\CoreInterceptorInterface;
use Spiral\Core\CoreInterface;
use Spiral\Core\Exception\ControllerException;
use Spiral\Core\FactoryInterface;
use Spiral\Core\InterceptableCore;
use Spiral\Core\Scope;
use Spiral\Core\ScopeInterface;
use Spiral\Domain\GuardInterceptor;
use Spiral\Domain\Permission;
use Spiral\Domain\PermissionsProviderInterface;
use Spiral\Interceptors\Context\CallContext;
use Spiral\Interceptors\Context\Target;
use Spiral\Interceptors\Handler\AutowireHandler;
use Spiral\Interceptors\HandlerInterface;
use Spiral\Interceptors\InterceptorInterface;
use Spiral\Interceptors\PipelineBuilderInterface;
use Spiral\Keeper\Config\KeeperEntitiesConfig;
use Spiral\Keeper\Exception\KeeperException;

/**
 * @implements InjectorInterface<object>
 */
final class KeeperCore implements CoreInterface, InjectorInterface, PermissionsProviderInterface
{
    /** @var array */
    private array $controllers = [];

    /** @var array */
    private array $aliases = [];

    private array $modules = [];

    private InterceptableCore $invoker;

    private PipelineBuilderInterface $builder;
    /** @var list<CoreInterceptorInterface|InterceptorInterface> */
    private array $interceptors = [];

    public function __construct(
        #[Proxy] private readonly ScopeInterface $scope,
        BinderInterface $binder,
        #[Proxy] private readonly FactoryInterface $factory,
        private readonly CoreInterface|HandlerInterface|null $core,
        private readonly PermissionsProviderInterface $permissions,
        private readonly string $namespace,
        KeeperEntitiesConfig $config,
        PipelineBuilderInterface $builder = null,
    ) {
        $this->builder = $builder ?? new CompatiblePipelineBuilder();
        $config->setCore($this);

        // Add guard interceptor
        $this->interceptors[] = $this->factory->make(GuardInterceptor::class, ['permissions' => $this]);

        // Init routes
        \array_map(fn(\Closure $closure) => $closure($this), $config->getRoutes());
    }

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
     * Add interceptor.
     */
    public function addInterceptor(string|Autowire|InterceptorInterface|CoreInterceptorInterface $interceptor): void
    {
        $this->interceptors[] = $this->initInterceptor($interceptor);
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
     * Inject a module
     */
    public function createInjection(\ReflectionClass $class, string $context = null): object
    {
        // To avoid a conflict when a few KeeperCore instances are registered as injectors for the same module
        $keeper = ContainerScope::getContainer()->get(self::class);
        return $keeper->getModule($class->getName());
    }

    /**
     * @param string $controller
     * @param string $action
     * @param array  $parameters
     * @return mixed
     * @throws \Throwable
     */
    public function callAction(string $controller, string $action, array $parameters = []): mixed
    {
        return $this->scope->runScope(
            new Scope(
                name: 'keeper',
                bindings: [
                    self::class          => $this,
                    CoreInterface::class => $this,
                ],
            ),
            function (ContainerInterface $container) use ($controller, $action, $parameters) {
                return $this->builder
                    ->withInterceptors(...$this->interceptors)
                    ->build($this->core ?? new AutowireHandler($container))
                    ->handle(
                        new CallContext(
                            Target::fromPair($controller, $action),
                            arguments: $parameters,
                        )
                    );
            }
        );
    }

    public function getPermission(string $controller, string $action): Permission
    {
        $permission = $this->permissions->getPermission($controller, $action);
        return $permission->ok ? $permission : Permission::ok(
            "{$this->namespace}.{$this->getControllerAlias($controller)}.$action",
            ControllerException::FORBIDDEN,
            "Unauthorized access `{$action}`"
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

    public function initInterceptor(
        string|Autowire|InterceptorInterface|CoreInterceptorInterface $interceptor,
    ): InterceptorInterface|CoreInterceptorInterface {
        return match (true) {
            $interceptor instanceof Autowire => $interceptor->resolve($this->factory),
            $interceptor instanceof CoreInterceptorInterface,
                $interceptor instanceof InterceptorInterface => $interceptor,
            \is_subclass_of($interceptor, CoreInterceptorInterface::class),
            \is_subclass_of($interceptor, InterceptorInterface::class) => $this->factory->make($interceptor),
            default => throw new KeeperException("Invalid interceptor definition `{$interceptor}`."),
        };
    }
}
