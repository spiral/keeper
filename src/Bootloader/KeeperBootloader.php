<?php

declare(strict_types=1);

namespace Spiral\Keeper\Bootloader;

use Psr\Http\Server\MiddlewareInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\BootloadManager\BootloadManager;
use Spiral\Boot\BootloadManager\ClassesRegistry;
use Spiral\Boot\BootloadManager\Initializer;
use Spiral\Bootloader\Security\GuardBootloader;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Core\Container;
use Spiral\Core\Container\SingletonInterface;
use Spiral\Core\Core;
use Spiral\Core\CoreInterface;
use Spiral\Domain\GuardInterceptor;
use Spiral\Domain\PermissionsProviderInterface;
use Spiral\Keeper\Config\KeeperConfig;
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

    protected const SINGLETONS = [
        KeeperCore::class => [self::class, 'missingCore']
    ];

    protected const LOAD         = [];
    protected const INTERCEPTORS = [];
    protected const MIDDLEWARE   = [];

    /** @var ConfiguratorInterface */
    protected $config;

    /** @var Container */
    protected $container;

    /** @var KeeperCore */
    protected $core;

    public function __construct(ConfiguratorInterface $config, Container $container)
    {
        $this->config = $config;
        $this->container = $container;
    }

    /**
     * Adds new keeper module and create keeper specific context dependency.
     */
    public function addModule(object $module, array $aliases = []): void
    {
        $aliases[] = get_class($module);
        $this->core->addModule($module, $aliases);

        foreach ($aliases as $alias) {
            // only inside the scope
            $this->container->bindInjector($alias, KeeperCore::class);
        }
    }

    /**
     * @param BootloadManager              $bootloadManager
     * @param RouterInterface              $appRouter
     * @param PermissionsProviderInterface $permissions
     * @throws \Throwable
     */
    public function boot(
        RouterInterface $appRouter,
        PermissionsProviderInterface $permissions,
        BootloadManager $bootloadManager,
        GroupRegistry $groups
    ): void {
        $keeeperBootloadManager = $this->getKeeperBootloadManager($bootloadManager->getClasses());

        $this->core = new KeeperCore(
            $this->container,
            new Core($this->container),
            $permissions,
            static::NAMESPACE
        );
        $config = $this->initConfig();

        // keeper relies on it's own routing mechanism
        $routes = new RouteRegistry($config, $appRouter, $groups);

        $this->addModule($routes, ['routes']);

        // init all keeper functionality
        $this->container->runScope(
            [
                self::class          => $this,
                CoreInterface::class => $this->core,
                KeeperCore::class    => $this->core,
                KeeperConfig::class  => $config
            ],
            function () use ($config, $keeeperBootloadManager): void {
                (clone $keeeperBootloadManager)->bootload($config->getModuleBootloaders());
                $this->initInterceptors($config);
            }
        );
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return static::NAMESPACE;
    }

    /**
     * @param string $controller
     * @param string $class
     */
    public function addController(string $controller, string $class): void
    {
        $this->core->setController($controller, $class);
    }

    /**
     * @param MiddlewareInterface|string $middleware
     */
    public function addMiddleware($middleware): void
    {
        $this->getRouteRegistry()->addMiddleware($middleware);
    }

    /**
     * @param string      $pattern
     * @param string      $controller
     * @param string      $action
     * @param array       $verbs
     * @param string|null $name
     * @param array       $defaults
     * @param string|null $group
     * @param array       $middlewares
     */
    public function addRoute(
        string $pattern,
        string $controller,
        string $action,
        array $verbs = Route::VERBS,
        string $name = null,
        array $defaults = [],
        string $group = null,
        array $middlewares = []
    ): void {
        $target = new Action($this->core->getController($controller), $action);
        $route = new Route($pattern, $target->withCore($this->core), $defaults);

        $route = $route->withMiddleware(...$middlewares)->withVerbs(...$verbs);
        if ($name !== null) {
            $this->getRouteRegistry()->setRoute($name, $route, $group);
        }

        $this->getRouteRegistry()->setRoute("$controller.$action", $route, $group);
    }

    protected function getMiddleware(): array
    {
        return static::MIDDLEWARE;
    }

    /**
     * @return RouteRegistry
     */
    protected function getRouteRegistry(): RouteRegistry
    {
        /** @var RouteRegistry $registry */
        $registry = $this->core->getModule(RouteRegistry::class);
        return $registry;
    }

    /**
     * @param KeeperConfig $config
     * @throws \Throwable
     */
    private function initInterceptors(KeeperConfig $config): void
    {
        foreach ($config->getInterceptors() as $interceptor) {
            if (is_object($interceptor) && !$interceptor instanceof Container\Autowire) {
                $this->core->addInterceptor($interceptor);
            } else {
                $this->core->addInterceptor($this->container->get($interceptor));
            }
        }

        $this->core->addInterceptor($this->container->make(GuardInterceptor::class, ['permissions' => $this->core]));
    }

    /**
     * Init configuration from default and user-defined value.
     *
     * @return KeeperConfig
     */
    private function initConfig(): KeeperConfig
    {
        $config = static::CONFIG_NAME ?: static::NAMESPACE;
        $this->config->setDefaults(
            $config,
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
            ]
        );

        return new KeeperConfig(static::NAMESPACE, $this->config->getConfig($config));
    }

    /**
     * No keeper found.
     */
    private function missingCore(): void
    {
        throw new KeeperException('Keeper core requested outside of its context');
    }

    private function getKeeperBootloadManager(array $classes): BootloadManager
    {
        $registry = new ClassesRegistry();
        foreach ($classes as $class) {
            if (!\is_subclass_of($class, KeeperBootloaderInterface::class)) {
                $registry->register($class);
            }
        }

        return new BootloadManager(
            $this->container,
            $this->container,
            $this->container,
            new Initializer($this->container, $this->container, $registry),
        );
    }
}
