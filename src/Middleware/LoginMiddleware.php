<?php

declare(strict_types=1);

namespace Spiral\Keeper\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Spiral\Auth\Middleware\Firewall\AbstractFirewall;
use Spiral\Views\ViewsInterface;

class LoginMiddleware extends AbstractFirewall
{
    public function __construct(
        private readonly string $loginView,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ViewsInterface $views,
    ) {}

    public function denyAccess(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(401);
        $response->getBody()->write($this->views->render($this->loginView));

        return $response;
    }
}
