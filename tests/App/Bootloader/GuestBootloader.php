<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Keeper\KeeperCore;
use Spiral\Security\Actor\Guest;
use Spiral\Security\PermissionsInterface;
use Spiral\Security\Rule;
use Spiral\Tests\Keeper\App\Enemy;

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

        $permissions->associate(Enemy::ROLE, "{$ns}.*", Rule\ForbidRule::class);
        $permissions->associate(Enemy::ROLE, "{$ns}.*.*", Rule\ForbidRule::class);
        $permissions->associate(Enemy::ROLE, "{$ns}.*.*.*", Rule\ForbidRule::class);
        $permissions->associate(Enemy::ROLE, "{$ns}.sitemap.index", Rule\AllowRule::class);
    }
}
