<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Defaults;

use Psr\Http\Message\ServerRequestInterface;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Router\Router;

/**
 * @Controller(namespace="default", name="known", prefix="/known", defaultAction="foo")
 */
class SetKnownController
{
    /**
     * @Action(route="/baz")
     * @return string
     */
    public function foo(): string
    {
        return 'known: foo';
    }

    /**
     * @Action(route="/defaults")
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function defaults(ServerRequestInterface $request)
    {
        return $request->getAttribute(Router::ROUTE_MATCHES);
    }
}
