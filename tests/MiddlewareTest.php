<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper;

use Psr\Http\Server\RequestHandlerInterface;
use Spiral\Auth\AuthContextInterface;
use Spiral\Auth\Middleware\AuthMiddleware;
use Spiral\Security\Actor\Guest;
use Spiral\Security\ActorInterface;
use Spiral\Tests\Keeper\App\Auth\AuthContext;

class MiddlewareTest extends TestCase
{
    use HttpTrait;

    public function testLoginFailed(): void
    {
        $response = $this->get('/login/login');
        $this->assertSame(401, $response->getStatusCode());
        $this->assertStringContainsString('Please, log in', (string)$response->getBody());
    }

    public function testLoginOk(): void
    {
        $this->runScoped(
            function (): void {
                $this->assertSame(
                    200,
                    $this->get(
                        '/login/login',
                        [],
                        [],
                        [],
                        [AuthMiddleware::ATTRIBUTE => new AuthContext()]
                    )->getStatusCode()
                );
            },
            [
                RequestHandlerInterface::class => $this->getContainer()->get(RequestHandlerInterface::class),
                AuthContextInterface::class    => AuthContext::class,
                ActorInterface::class          => Guest::class
            ],
        );
    }
}
