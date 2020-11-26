<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Auth;

use Spiral\Auth\AuthContextInterface;
use Spiral\Auth\Session\Token;
use Spiral\Auth\TokenInterface;
use Spiral\Security\Actor\Guest;

class AuthContext implements AuthContextInterface
{
    public const TOKEN_ID = 'tokeID';

    public function start(TokenInterface $token, string $transport = null): void
    {
        // TODO: Implement start() method.
    }

    public function getToken(): ?TokenInterface
    {
        return new Token(self::TOKEN_ID, []);
    }

    public function getTransport(): ?string
    {
        // TODO: Implement getTransport() method.
    }

    public function getActor(): ?object
    {
        return new Guest();
    }

    public function close(): void
    {
        // TODO: Implement close() method.
    }

    public function isClosed(): bool
    {
        // TODO: Implement isClosed() method.
    }
}
