<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Sitemap;

use Spiral\Tests\Keeper\HttpTrait;
use Spiral\Tests\Keeper\TestCase;

class AnnotationTest extends TestCase
{
    use HttpTrait;

    public function testRoot(): void
    {
        $response = $this->get('/default/root/self');
        dump((string)$response->getStatusCode());
        dump((string)$response->getBody());
    }
}
