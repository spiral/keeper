<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Sitemap;

use Spiral\Tests\Keeper\HttpTrait;
use Spiral\Tests\Keeper\TestCase;

class SitemapTest extends TestCase
{
    use HttpTrait;

    public function testForbidden(): void
    {
        $output = $this->getSitemap();
        $nodes = array_map(
            static function (array $node): string {
                return $node['name'];
            },
            $output
        );

        $this->assertCount(4, $nodes);
        $this->assertContains('custom', $nodes);
        $this->assertContains('custom.parent', $nodes);
        $this->assertContains('root.parent', $nodes);
        $this->assertContains('root.child', $nodes);
    }

    private function getSitemap(): array
    {
        $response = $this->get('/default/root/child');
        return json_decode((string)$response->getBody(), true);
    }
}
