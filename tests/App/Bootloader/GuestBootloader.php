<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Keeper\KeeperCore;
use Spiral\Security\Actor\Guest;
use Spiral\Security\PermissionsInterface;
use Spiral\Security\Rule;
use Spiral\Tests\Keeper\App\Auth\Enemy;

class GuestBootloader extends Bootloader
{
    public function boot(KeeperCore $core, PermissionsInterface $permissions): void
    {
        if (!$permissions->hasRole(Guest::ROLE)) {
            $permissions->addRole(Guest::ROLE);
        }
        if (!$permissions->hasRole(Enemy::ROLE)) {
            $permissions->addRole(Enemy::ROLE);
        }

        $ns = $core->getNamespace();
        $permissions->associate(Guest::ROLE, "{$ns}.*", Rule\AllowRule::class);
        $permissions->associate(Guest::ROLE, "{$ns}.*.*", Rule\AllowRule::class);
        $permissions->associate(Guest::ROLE, "{$ns}.*.*.*", Rule\AllowRule::class);
        $permissions->associate(Guest::ROLE, 'root.*', Rule\AllowRule::class);
        $permissions->associate(Guest::ROLE, 'im-a-child', Rule\AllowRule::class);
        $permissions->associate(Guest::ROLE, 'default.external.*', Rule\AllowRule::class);

        $permissions->associate(Guest::ROLE, 'guarded.provided.allowed', Rule\AllowRule::class);
        $permissions->associate(Guest::ROLE, 'guarded.provided.forbidden', Rule\ForbidRule::class);
        $permissions->associate(Guest::ROLE, 'guarded.missing.allowed', Rule\AllowRule::class);
        $permissions->associate(Guest::ROLE, 'guarded.missing.forbidden', Rule\ForbidRule::class);

        $permissions->associate(Enemy::ROLE, "{$ns}.*", Rule\AllowRule::class);
        $permissions->associate(Enemy::ROLE, "{$ns}.*.*", Rule\AllowRule::class);
        $permissions->associate(Enemy::ROLE, "{$ns}.*.*.*", Rule\AllowRule::class);
        $permissions->associate(Enemy::ROLE, 'root.*', Rule\AllowRule::class);
        $permissions->associate(Enemy::ROLE, 'im-a-child', Rule\AllowRule::class);
        $permissions->associate(Enemy::ROLE, 'default.external.*', Rule\AllowRule::class);
        $permissions->associate(Enemy::ROLE, 'default.external.linkAllowed', Rule\AllowRule::class);

        $permissions->associate(Enemy::ROLE, 'default.root.parentRoot', Rule\ForbidRule::class);
        $permissions->associate(Enemy::ROLE, 'default.external.cstm', Rule\ForbidRule::class);
        $permissions->associate(Enemy::ROLE, 'default.external.linkForbidden', Rule\ForbidRule::class);
    }
}
