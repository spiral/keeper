<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Auth;

use Spiral\Security\ActorInterface;

class Enemy implements ActorInterface
{
    public const ROLE = 'enemy';

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return [static::ROLE];
    }
}
