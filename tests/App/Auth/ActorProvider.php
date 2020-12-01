<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Auth;

use Spiral\Auth\ActorProviderInterface;
use Spiral\Auth\TokenInterface;
use Spiral\Security\Actor\Actor;

class ActorProvider implements ActorProviderInterface
{
    public function getActor(TokenInterface $token): ?object
    {
        return new Actor([Enemy::ROLE]);
    }
}
