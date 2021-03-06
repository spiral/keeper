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
use Spiral\Bootloader\Views\ViewsBootloader;
use Spiral\Keeper\Directive\ActionDirective;
use Spiral\Keeper\Directive\AuthDirective;
use Spiral\Stempler\Bootloader\StemplerBootloader;
use Spiral\Toolkit\Bootloader\ToolkitBootloader;

final class UIBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        StemplerBootloader::class,
        ToolkitBootloader::class
    ];

    public function boot(ViewsBootloader $views, StemplerBootloader $stempler): void
    {
        // overwrite
        $views->addDirectory('keeper', directory('views') . '/keeper');
        $views->addDirectory('keeper', dirname(__DIR__) . '/../views');

        $stempler->addDirective(ActionDirective::class);
        $stempler->addDirective(AuthDirective::class);
    }
}
