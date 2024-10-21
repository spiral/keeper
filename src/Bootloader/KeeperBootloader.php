<?php

declare(strict_types=1);

namespace Spiral\Keeper\Bootloader;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\BootloadManager\ClassesRegistry;
use Spiral\Boot\BootloadManager\DefaultInvokerStrategy;
use Spiral\Boot\BootloadManager\Initializer;
use Spiral\Boot\BootloadManager\StrategyBasedBootloadManager;
use Spiral\Boot\BootloadManagerInterface;
use Spiral\Bootloader\Security\GuardBootloader;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Core\BinderInterface;
use Spiral\Core\Container;
use Spiral\Core\Container\SingletonInterface;
use Spiral\Core\CoreInterface;
use Spiral\Core\FactoryInterface;
use Spiral\Core\InvokerInterface;
use Spiral\Core\ResolverInterface;
use Spiral\Core\ScopeInterface;
use Spiral\Domain\GuardInterceptor;
use Spiral\Keeper\Config\KeeperConfig;
use Spiral\Keeper\Config\KeeperEntitiesConfig;
use Spiral\Keeper\Exception\KeeperException;
use Spiral\Keeper\KeeperCore;
use Spiral\Keeper\Module\RouteRegistry;
use Spiral\Router\GroupRegistry;
use Spiral\Router\Route;
use Spiral\Router\RouterInterface;
use Spiral\Router\Target\Action;

abstract class KeeperBootloader extends Bootloader implements SingletonInterface, KeeperBootloaderInterface
{
    protected const NAMESPACE          = 'keeper';
    protected const PREFIX             = 'keeper/';
    protected const DEFAULT_CONTROLLER = 'dashboard';
    protected const CONFIG_NAME        = '';

    protected const DEPENDENCIES = [
        GuardBootloader::class,
    ];

    protected const LOAD         = [];
    protected const INTERCEPTORS = [];
    protected const MIDDLEWARE   = [];

    /** @deprecated don't use it */
    protected $container;

    /** @deprecated don't use it */
    protected $core;

    private KeeperEntitiesConfig $config;
    private BinderInterface $binder;
    private BootloadManagerInterface $bootloadManager;

    public function defineSingletons(): array
    {
        return [
            self::class => static fn() => throw new KeeperException(
                'Keeper core requested outside of its context',
            ),
        ];
    }

    /**
     * Init configuration from default and user-defined value.
     */
    private function init(ConfiguratorInterface $configurator, BinderInterface $binder): void
    {
        $this->config = new KeeperEntitiesConfig();
        $this->binder = $binder;
        $configName = static::CONFIG_NAME ?: static::NAMESPACE;
        $configurator->setDefaults(
            $configName,
            [
                // keeper isolation prefix (only for non-host routing)
                'routePrefix'   => static::PREFIX,

                // default controller
                'routeDefaults' => ['controller' => static::DEFAULT_CONTROLLER],

                // page to render when login is required
                'loginView'     => 'keeper:login',

                // global keeper middleware
                'middleware'    => $this->getMiddleware(),

                // connected modules and extensions
                'modules'       => static::LOAD,

                // domain Core interceptors
                'interceptors'  => static::INTERCEPTORS,
            ],
        );

        // Store shared object
        $binder->bind(KeeperConfig::class, new KeeperConfig(static::NAMESPACE, $configurator->getConfig($configName)));
    }

    /**
     * @throws \Throwable
     */
    public function boot(
        InvokerInterface $invoker,
        RouterInterface $appRouter,
        ScopeInterface $scope,
        GroupRegistry $groups,
        KeeperConfig $config,
        FactoryInterface $factory,
    ): void {
        $bootloadManager = $invoker->invoke($this->getKeeperBootloadManager(...));

        // keeper relies on it's own routing mechanism
        $routes = new RouteRegistry($config, $appRouter, $groups);
        $this->addModule($routes, ['routes']);

        $core = (new Container\Autowire(KeeperCore::class, [
            'namespace' => static::NAMESPACE,
            'config' => $this->config,
        ]))->resolve($factory);

        // init all keeper functionality
        $scope->runScope(
            [
                self::class          => $this,
                CoreInterface::class => $core,
                KeeperCore::class    => $core,
                KeeperConfig::class  => $config
            ],
            function (ContainerInterface $container) use ($config, $bootloadManager, $core): void {
                // init all keeper functionality
                $bootloadManager->bootload($config->getModuleBootloaders());
                $this->initInterceptors($container, $core, $config);
            }
        );
    }

    private function initInterceptors(ContainerInterface $container, KeeperCore $core, KeeperConfig $config): void
    {
        foreach ($config->getInterceptors() as $interceptor) {
            $core->addInterceptor($interceptor);
        }

        $core->addInterceptor($container->make(GuardInterceptor::class, ['permissions' => $core]));
    }

    /**
     * Adds new keeper module and create keeper specific context dependency.
     */
    public function addModule(
        object $module,
        array $aliases = [],
    ): void {
        /** @var array<class-string> $aliases */
        $aliases[] = $module::class;
        foreach ($aliases as $alias) {
            $this->binder->bindInjector($alias, KeeperCore::class);
        }

        $this->config->addModule($module, $aliases);
    }

    public function getNamespace(): string
    {
        return static::NAMESPACE;
    }

    public function addController(string $controller, string $class): void
    {
        $this->config->addController($controller, $class);
    }

    public function addMiddleware(MiddlewareInterface|string $middleware): void
    {
        $this->config->addMiddleware($middleware);
    }

    public function addRoute(
        string $pattern,
        string $controller,
        string $action,
        array $verbs = Route::VERBS,
        string $name = null,
        array $defaults = [],
        string $group = null,
        array $middlewares = [],
    ): void {
        $this->config->addRouteRegistrar(static function(KeeperCore $core) use(
            $pattern,
            $controller,
            $action,
            $verbs,
            $name,
            $defaults,
            $group,
            $middlewares,
        ) {
            /** @var RouteRegistry $registry */
            $registry = $core->getModule(RouteRegistry::class);

            $target = new Action($core->getController($controller), $action);
            $route = new Route($pattern, $target->withCore($core), $defaults);

            $route = $route->withMiddleware(...$middlewares)->withVerbs(...$verbs);
            if ($name !== null) {
                $registry->setRoute($name, $route, $group);
            }

            $registry->setRoute("$controller.$action", $route, $group);
        });
    }

    protected function getMiddleware(): array
    {
        return static::MIDDLEWARE;
    }

    protected function getRouteRegistry(): RouteRegistry
    {
        throw new KeeperException('Method `getRouteRegistry()` is not provided since Keeper v0.11.');
    }

    private function getKeeperBootloadManager(
        BootloadManagerInterface $bootloadManager,
        ContainerInterface $container,
        BinderInterface $binder,
        InvokerInterface $invoker,
        ResolverInterface $resolver,
        ScopeInterface $scope,
    ): BootloadManagerInterface {
        if (isset($this->bootloadManager)) {
            return $this->bootloadManager;
        }

        $classes = $bootloadManager->getClasses();
        $registry = new ClassesRegistry();
        foreach ($classes as $class) {
            if (!\is_subclass_of($class, KeeperBootloaderInterface::class)) {
                $registry->register($class);
            }
        }

        $initializer = new Initializer($container, $binder, $registry);

        return $this->bootloadManager = new StrategyBasedBootloadManager(
            new DefaultInvokerStrategy($initializer, $invoker, $resolver),
            $scope,
            $initializer,
        );
    }
}
