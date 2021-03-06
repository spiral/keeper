<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Keeper\Module\Sitemap;

class SitemapBootloader extends \Spiral\Keeper\Bootloader\SitemapBootloader
{
    protected function declareSitemap(Sitemap $sitemap): void
    {
        $group = $sitemap->group('custom', 'Custom Group', ['position' => 1.1]);
        $group->link('root.duplicated', 'Duplicated link');
        $group->link('custom.parent', 'custom parent');
    }
}
