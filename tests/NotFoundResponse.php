<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper;

use Laminas\Diactoros\Response\JsonResponse;

class NotFoundResponse extends JsonResponse
{
    public function __construct()
    {
        parent::__construct([], 404);
    }
}
