<?php

/**
 * This file is part of Spiral Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Tests\Keeper;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Spiral\Boot\Environment;
use Spiral\Router\RouterInterface;
use Spiral\Tests\Keeper\App\App;

abstract class TestCase extends BaseTestCase
{
    /** @var App */
    protected $app;

    /**
     * @throws \Throwable
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->app = $this->makeApp(['DEBUG' => true]);
    }

    /**
     * @param array $env
     * @return App
     * @throws \Throwable
     */
    protected function makeApp(array $env = []): App
    {
        $config = [
            'config' => __DIR__ . '/config/',
            'root'   => __DIR__ . '/App/',
            'views'  => __DIR__ . '/views/',
            'app'    => __DIR__ . '/App/',
        ];

        return App::create($config)->run(new Environment($env));
    }

    protected function router(): RouterInterface
    {
        return $this->app->get(RouterInterface::class);
    }
}
