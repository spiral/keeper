<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Spiral\Auth\Middleware\Firewall\AbstractFirewall;
use Spiral\Keeper\Config\KeeperConfig;
use Spiral\Views\ViewsInterface;

class LoginMiddleware extends AbstractFirewall
{
    /** @var KeeperConfig */
    private $config;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var ViewsInterface */
    private $views;

    /**
     * @param KeeperConfig             $config
     * @param ResponseFactoryInterface $responseFactory
     * @param ViewsInterface           $views
     */
    public function __construct(
        KeeperConfig $config,
        ResponseFactoryInterface $responseFactory,
        ViewsInterface $views
    ) {
        $this->config = $config;
        $this->responseFactory = $responseFactory;
        $this->views = $views;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function denyAccess(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(401);
        $response->getBody()->write($this->views->render($this->config->getLoginView()));

        return $response;
    }
}
