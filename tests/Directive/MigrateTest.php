<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Directive;

use Spiral\Tests\Keeper\HttpTrait;
use Spiral\Tests\Keeper\TestCase;

class MigrateTest extends TestCase
{
    use HttpTrait;

    public function testOldKeeperDirective(): void
    {
        $this->assertSame(
            '<a href="/old/old/old">old.old</a>',
            $this->getContent('/old/old/old')
        );
    }

    public function testNewKeeperDirective(): void
    {
        $this->assertSame(
            '<a href="/old/old/new">old.new</a>',
            $this->getContent('/old/old/new')
        );
    }

    public function testNewDirective(): void
    {
        $this->assertSame(
            '<a href="/new/new/new">new.new</a><a href="/new/new/new">new.new</a>',
            $this->getContent('/new/new/new')
        );
    }

    public function testMixedRoutes(): void
    {
        $this->assertSame(
            '<a href="/new/new/mixed">new.mixed</a><a href="/new/new/mixed">new:mixed</a>',
            $this->getContent('/new/new/mixed')
        );
    }

    private function getContent(string $url): string
    {
        return trim($this->get($url)->getBody()->__toString());
    }
}
