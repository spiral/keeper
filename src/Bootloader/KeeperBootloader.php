<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Bootloader;

use Psr\Http\Server\MiddlewareInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\BootloadManager;
use Spiral\Bootloader\Security\GuardBootloader;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Core\Container;
use Spiral\Core\Container\SingletonInterface;
use Spiral\Core\Core;
use Spiral\Core\CoreInterface;
use Spiral\Keeper\Config\KeeperConfig;
use Spiral\Keeper\Exception\KeeperException;
use Spiral\Keeper\KeeperCore;
use Spiral\Keeper\Module\RouteRegistry;
use Spiral\Router\Route;
use Spiral\Router\RouterInterface;
use Spiral\Router\Target\Action;
use Spiral\Security\GuardInterface;

abstract class KeeperBootloader extends Bootloader implements SingletonInterface
{
    protected const NAMESPACE          = 'keeper';
    protected const PREFIX             = 'keeper/';
    protected const DEFAULT_CONTROLLER = 'dashboard';

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

    /**
     * @param ConfiguratorInterface $config
     * @param GuardInterface        $guard
     * @param Container             $container
     */
    public function __construct(ConfiguratorInterface $config, GuardInterface $guard, Container $container)
    {
        $this->config = $config;
        $this->container = $container;
        $this->core = new KeeperCore(
            $container,
            new Core($container),
            $guard,
            static::NAMESPACE
        );
    }

    /**
     * Adds new keeper module and create keeper specific context dependency.
     *
     * @param object $module
     * @param array  $aliases
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
     * @param BootloadManager $bootloadManager
     * @param RouterInterface $appRouter
     * @throws \Throwable
     */
    public function boot(BootloadManager $bootloadManager, RouterInterface $appRouter): void
    {
        $config = $this->initConfig(static::NAMESPACE);

        // keeper relies on it's own routing mechanism
        $routes = new RouteRegistry($this->container, $config);

        $this->addModule($routes, ['routes']);

        // init all keeper functionality
        $this->container->runScope(
            [
                self::class          => $this,
                CoreInterface::class => $this->core,
                KeeperCore::class    => $this->core,
                KeeperConfig::class  => $config
            ],
            function () use ($config, $bootloadManager, $appRouter, $routes): void {
                (clone $bootloadManager)->bootload($config->getModuleBootloaders());
                $this->initInterceptors($config);

                $appRouter->setRoute(static::NAMESPACE, $routes->initEndpoint());
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
     * @param string $pattern
     * @param string $controller
     * @param string $action
     * @param array  $verbs
     */
    public function addRoute(string $pattern, string $controller, string $action, array $verbs = Route::VERBS): void
    {
        $target = new Action($controller, $action);

        $this->getRouteRegistry()->setRoute(
            sprintf('%s.%s', $controller, $action),
            (new Route($pattern, $target->withCore($this->core)))->withVerbs(...$verbs)
        );
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
    }

    /**
     * Init configuration from default and user-defined value.
     *
     * @param string $namespace
     * @return KeeperConfig
     */
    private function initConfig(string $namespace): KeeperConfig
    {
        $this->config->setDefaults(
            $namespace,
            [
                // keeper isolation prefix (only for non-host routing)
                'routePrefix'   => static::PREFIX,

                // default controller
                'routeDefaults' => ['controller' => static::DEFAULT_CONTROLLER],

                // page to render when login is required
                'loginView'     => 'keeper:login',

                // keeper route must react to all the path variations
                'routePattern'  => sprintf('%s[/<path:.*>]', $namespace),

                // global keeper middleware
                'middleware'    => static::MIDDLEWARE,

                // connected modules and extensions
                'modules'       => static::LOAD,

                // domain Core interceptors
                'interceptors'  => static::INTERCEPTORS,
            ]
        );

        return new KeeperConfig($this->config->getConfig($namespace));
    }

    /**
     * No keeper found.
     */
    private function missingCore(): void
    {
        throw new KeeperException('Keeper core requested outside of its context');
    }
}
