<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Sitemap;

use Spiral\Security\Actor\Guest;
use Spiral\Security\ActorInterface;
use Spiral\Tests\Keeper\App\Auth\Enemy;
use Spiral\Tests\Keeper\HttpTrait;
use Spiral\Tests\Keeper\TestCase;

class AnnotationTest extends TestCase
{
    use HttpTrait;

    public function testOrder(): void
    {
        $output = $this->getSitemap();
        $groupPositions = [];
        foreach ($output as $name => $group) {
            $groupPositions[$name] = $group['options']['position'] ?? 0;
        }
        $rootSame = [
            'externalgroup' => 0,
            'simple.method' => 0.8,
            'rootgroup'     => 1,
            'custom'        => 1.1
        ];
        $this->assertSame($rootSame, $groupPositions);

        $rootGroupNodes = [];
        foreach ($output['rootgroup']['nodes'] as $name => $group) {
            $rootGroupNodes[$name] = $group['options']['position'] ?? 0;
        }
        $nestedSame = [
            'root.top'   => 0.6,
            'root.index' => 0.7
        ];
        $this->assertSame($nestedSame, $rootGroupNodes);
    }

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

    /**
     * @depends testExternalParent
     */
    public function testPermissionsWithoutNamespace(): void
    {
        $output = $this->getSitemap();

        $parent = $this->node($output, 'custom', 'custom.parent', 'root.parent');
        $this->assertSame('root.parentRoot', $parent['options']['permission']);

        $child = $this->node($output, 'custom', 'custom.parent', 'root.parent', 'root.child');
        $this->assertSame('root.im-a-child', $child['options']['permission']);
    }

    /**
     * @depends testExternalParent
     * @depends testSiblings
     */
    public function testPermissionsWithNamespace(): void
    {
        $output = $this->getSitemap();

        $node = $this->node($output, 'custom', 'custom.parent', 'root.parent', 'root.child', 'external.index');
        $this->assertSame('default.external.index', $node['options']['permission']);

        $node = $this->node($output, 'custom', 'custom.parent', 'external.custom');
        $this->assertSame('default.external.cstm', $node['options']['permission']);
    }

    public function testForbidden(): void
    {
        $this->app->runScope(
            [ActorInterface::class => Enemy::class],
            function () use (&$output): void {
                $output = $this->getSitemap(true);
            }
        );

        // This exact action is forbidden tests/App/Bootloader/GuestBootloader.php:34
        $this->assertArrayNotHasKey('root.parent', $this->nodes($output, 'custom', 'custom.parent'));
        $this->assertArrayNotHasKey('external.custom', $this->nodes($output, 'custom', 'custom.parent'));
        $this->assertArrayHasKey('root.bottom', $this->nodes($output, 'rootgroup', 'root.top'));

        $this->app->runScope(
            [ActorInterface::class => Guest::class],
            function () use (&$output): void {
                $output = $this->getSitemap(true);
            }
        );

        $this->assertArrayHasKey('root.parent', $this->nodes($output, 'custom', 'custom.parent'));
        $this->assertArrayHasKey('external.custom', $this->nodes($output, 'custom', 'custom.parent'));
        $this->assertArrayHasKey('root.bottom', $this->nodes($output, 'rootgroup', 'root.top'));
    }

    private function getSitemap(bool $onlyVisible = false): array
    {
        $response = $this->get($onlyVisible ? '/default/sitemap/visible' : '/default/sitemap');
        return json_decode((string)$response->getBody(), true);
    }

    private function node(array $sitemap, string $firstKey, string ...$keys): array
    {
        $sitemap = ['nodes' => $sitemap];
        array_unshift($keys, $firstKey);
        foreach ($keys as $key) {
            $sitemap = $sitemap['nodes'][$key];
        }
        return $sitemap;
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
