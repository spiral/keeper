<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Keeper\Module\Sitemap;

class SitemapBootloader extends \Spiral\Keeper\Bootloader\SitemapBootloader
{
    protected function declareCustomSitemap(Sitemap $sitemap): void
    {
        $group = $sitemap->group('custom', 'Custom Group');
        $group->link('custom.parent', 'custom parent');
    }
}
