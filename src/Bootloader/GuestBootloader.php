<?php

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
final class GuestBootloader extends Bootloader implements KeeperBootloaderInterface
{
    /**
     * @param KeeperCore           $core
     * @param PermissionsInterface $permissions
     */
    public function boot(KeeperCore $core, PermissionsInterface $permissions): void
    {
        if (!$permissions->hasRole(Guest::ROLE)) {
            $permissions->addRole(Guest::ROLE);
        }
        if (!$permissions->hasRole('admin')) {
            $permissions->addRole('admin');
        }

        $ns = $core->getNamespace();
        $permissions->associate(Guest::ROLE, "{$ns}.*", AllowRule::class);
        $permissions->associate(Guest::ROLE, "{$ns}.*.*", AllowRule::class);
        $permissions->associate(Guest::ROLE, "{$ns}.*.*.*", AllowRule::class);

        $permissions->associate('admin', "{$ns}.*", AllowRule::class);
        $permissions->associate('admin', "{$ns}.*.*", AllowRule::class);
        $permissions->associate('admin', "{$ns}.*.*.*", AllowRule::class);
    }
}
