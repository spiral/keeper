<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Directive;

use Spiral\Auth\ActorProviderInterface;
use Spiral\Auth\AuthContextInterface;
use Spiral\Tests\Keeper\App\Auth\ActorProvider;
use Spiral\Tests\Keeper\App\Auth\AuthContext;
use Spiral\Tests\Keeper\TestCase;

class DirectiveTest extends TestCase
{
    public function testEmptyLogout(): void
    {
        $route = $this->fakeHttp()->get('/default/view');

        $route->assertBodyContains('<a href="/default/logout">1</a>');
        $route->assertBodyContains('<a href="/default/logout">2</a>');
        $route->assertBodyContains('<a href="/default/logout?a=b">3</a>');
    }

    public function testLogoutWithToken(): void
    {
        $this->runScoped(
            function (): void {
                $route = $this->fakeHttp()->get('/default/view');

                $route->assertBodyContains('<a href="/default/logout?token=' . AuthContext::TOKEN_ID . '">1</a>');
                $route->assertBodyContains('<a href="/default/logout?token=' . AuthContext::TOKEN_ID . '">2</a>');
                $route->assertBodyContains('<a href="/default/logout?a=b&token=' . AuthContext::TOKEN_ID . '">3</a>');
            },
            [
                AuthContextInterface::class   => AuthContext::class,
                ActorProviderInterface::class => ActorProvider::class
            ],
        );
    }

    public function testInvalidOld(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionMessageMatches('/^Unable to call .action directive/i');
        $this->fakeHttp()->get('/old/old/invalid');
    }

    public function testInvalidNew(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionMessageMatches('/^Unable to call .keeper directive/i');
        $this->fakeHttp()->get('/new/new/invalid');
    }
}
