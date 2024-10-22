<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Defaults;

use Psr\Http\Message\ServerRequestInterface;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Router\Router;

#[Controller(name: "known", prefix: "/known", namespace: "default", defaultAction: "foo")]
class SetKnownController
{
    #[Action(route: "/baz")]
    public function foo(): string
    {
        return 'known: foo';
    }

    #[Action(route: "/defaults")]
    public function defaults(ServerRequestInterface $request)
    {
        return $request->getAttribute(Router::ROUTE_MATCHES);
    }
}
