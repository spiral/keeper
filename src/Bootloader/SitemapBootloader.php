<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Keeper\Module\Sitemap;

final class SitemapBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        UIBootloader::class,
    ];

    /**
     * @param KeeperBootloader $keeper
     */
    public function boot(KeeperBootloader $keeper): void
    {
        $keeper->addModule(new Sitemap($keeper->getNamespace()), ['sitemap']);
    }
}
