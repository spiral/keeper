<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper;

use Spiral\Http\Exception\ClientException\ForbiddenException;

class GuardTest extends TestCase
{
    use HttpTrait;

    public function testAnnotated(): void
    {
        $this->assertSame(200, $this->getStatusCode('/guarded/provided/allowed'));
        $this->assertSame(403, $this->getStatusCode('/guarded/provided/forbidden'));
    }

    public function testFallback(): void
    {
        $this->assertSame(200, $this->getStatusCode('/guarded/missing/allowed'));
        $this->assertSame(403, $this->getStatusCode('/guarded/missing/forbidden'));
    }

    private function getStatusCode(string $uri): int
    {
        try {
            return $this->get($uri)->getStatusCode();
        } catch (ForbiddenException $e) {
            return 403;
        }
    }
}
