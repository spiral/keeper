<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Sitemap;

use Spiral\Security\ActorInterface;
use Spiral\Tests\Keeper\App\Enemy;
use Spiral\Tests\Keeper\HttpTrait;
use Spiral\Tests\Keeper\TestCase;

class AnnotationTest extends TestCase
{
    use HttpTrait;

    public function testRoot(): void
    {
        $output = $this->getSitemap();
        $this->assertArrayHasKey('simple.method', $output);
    }

    public function testGroup(): void
    {
        $output = $this->getSitemap();
        $this->assertArrayHasKey('rootgroup', $output);
    }

    public function testCustomDeclaring(): void
    {
        $output = $this->getSitemap();
        $this->assertArrayHasKey('custom', $output);
        $this->assertArrayHasKey('custom.parent', $this->nodes($output, 'custom'));
    }

    public function testLocalParent(): void
    {
        $output = $this->getSitemap();
        $this->assertArrayHasKey('rootgroup', $output);
        $this->assertArrayHasKey('root.top', $this->nodes($output, 'rootgroup'));
        $this->assertArrayHasKey('root.bottom', $this->nodes($output, 'rootgroup', 'root.top'));
    }

    public function testExternalParent(): void
    {
        $output = $this->getSitemap();

        //annotation -> custom sitemap
        $this->assertArrayHasKey('custom', $output);
        $this->assertArrayHasKey('custom.parent', $this->nodes($output, 'custom'));
        $this->assertArrayHasKey('root.parent', $this->nodes($output, 'custom', 'custom.parent'));

        //annotation -> annotation
        $this->assertArrayHasKey('root.child', $this->nodes($output, 'custom', 'custom.parent', 'root.parent'));
        $this->assertArrayHasKey(
            'external.index',
            $this->nodes($output, 'custom', 'custom.parent', 'root.parent', 'root.child')
        );
    }

    public function testSiblings(): void
    {
        $output = $this->getSitemap();
        $this->assertArrayHasKey('custom', $output);
        $this->assertArrayHasKey('custom.parent', $this->nodes($output, 'custom'));
        $this->assertArrayHasKey('root.parent', $this->nodes($output, 'custom', 'custom.parent'));
        $this->assertArrayHasKey('external.custom', $this->nodes($output, 'custom', 'custom.parent'));
    }

    public function testForbidden(): void
    {
        $this->app->runScope(
            [
                ActorInterface::class => Enemy::class,
            ],
            function () use (&$output): void {
                $output = $this->getSitemap();
            }
        );
        $this->assertArrayHasKey('custom', $output);
    }

    private function getSitemap(): array
    {
        $response = $this->get('/default/sitemap');
        return json_decode((string)$response->getBody(), true);
    }

    private function nodes(array $sitemap, string $firstKey, string ...$keys): array
    {
        array_unshift($keys, $firstKey);
        foreach ($keys as $key) {
            $sitemap = $sitemap[$key]['nodes'];
        }
        return $sitemap;
    }
}
