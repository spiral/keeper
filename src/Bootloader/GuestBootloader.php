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
use Spiral\Keeper\KeeperCore;
use Spiral\Security\Actor\Guest;
use Spiral\Security\PermissionsInterface;
use Spiral\Security\Rule\AllowRule;

/**
 * Provides full access to admin panel for Guests.
 */
final class GuestBootloader extends Bootloader
{
    /**
     * @param KeeperCore           $core
     * @param PermissionsInterface $permissions
     */
    public function boot(KeeperCore $core, PermissionsInterface $permissions): void
    {
        $permissions->addRole(Guest::ROLE);
        $permissions->addRole('admin');

        $ns = $core->getNamespace();
        $permissions->associate(Guest::ROLE, "{$ns}.*", AllowRule::class);
        $permissions->associate(Guest::ROLE, "{$ns}.*.*", AllowRule::class);
        $permissions->associate(Guest::ROLE, "{$ns}.*.*.*", AllowRule::class);

        $permissions->associate('admin', "{$ns}.*", AllowRule::class);
        $permissions->associate('admin', "{$ns}.*.*", AllowRule::class);
        $permissions->associate('admin', "{$ns}.*.*.*", AllowRule::class);
    }
}
