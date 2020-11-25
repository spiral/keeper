<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper;

use Spiral\Auth\ActorProviderInterface;
use Spiral\Auth\AuthContextInterface;
use Spiral\Tests\Keeper\App\Auth\ActorProvider;
use Spiral\Tests\Keeper\App\Auth\AuthContext;

class DirectiveTest extends TestCase
{
    use HttpTrait;

    public function testEmptyLogout(): void
    {
        $this->assertSame(
            '<a href="/default/logout">1</a>'
            . '<a href="/default/logout">2</a>'
            . '<a href="/default/logout?a=b">3</a>',
            $this->getContent('/default/view')
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
                    '<a href="/default/logout?token=' . AuthContext::TOKEN_ID . '">1</a>'
                    . '<a href="/default/logout?token=' . AuthContext::TOKEN_ID . '">2</a>'
                    . '<a href="/default/logout?a=b&token=' . AuthContext::TOKEN_ID . '">3</a>',
                    $this->getContent('/default/view')
                );
            }
        );
    }

    private function getContent(string $url): string
    {
        return trim(preg_replace('/\n/', '', $this->get($url)->getBody()->__toString()));
    }
}
