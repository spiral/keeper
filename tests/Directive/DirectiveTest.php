<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Directive;

use Spiral\Auth\ActorProviderInterface;
use Spiral\Auth\AuthContextInterface;
use Spiral\Tests\Keeper\App\Auth\ActorProvider;
use Spiral\Tests\Keeper\App\Auth\AuthContext;
use Spiral\Tests\Keeper\HttpTrait;
use Spiral\Tests\Keeper\TestCase;

class DirectiveTest extends TestCase
{
    use HttpTrait;

    public function testEmptyLogout(): void
    {
        $this->assertSame(
            \preg_replace('/\s+/', '', '<a href="/default/logout">1</a>'
            . '<a href="/default/logout">2</a>'
            . '<a href="/default/logout?a=b">3</a>'),
            \preg_replace('/\s+/', '', $this->getContent('/default/view'))
        );
    }

    public function testLogoutWithToken(): void
    {
        $this->app->runScope(
            [
                AuthContextInterface::class   => AuthContext::class,
                ActorProviderInterface::class => ActorProvider::class
            ],
            function (): void {
                $this->assertSame(
                    \preg_replace('/\s+/', '', '<a href="/default/logout?token=' . AuthContext::TOKEN_ID . '">1</a>'
                    . '<a href="/default/logout?token=' . AuthContext::TOKEN_ID . '">2</a>'
                    . '<a href="/default/logout?a=b&token=' . AuthContext::TOKEN_ID . '">3</a>'),
                    \preg_replace('/\s+/', '', $this->getContent('/default/view'))
                );
            }
        );
    }

    public function testInvalidOld(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionMessageMatches('/^Unable to call .action directive/i');
        $this->getContent('/old/old/invalid');
    }

    public function testInvalidNew(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectExceptionMessageMatches('/^Unable to call .keeper directive/i');
        $this->getContent('/new/new/invalid');
    }

    private function getContent(string $url): string
    {
        return trim(preg_replace('/\n/', '', $this->get($url)->getBody()->__toString()));
    }
}
