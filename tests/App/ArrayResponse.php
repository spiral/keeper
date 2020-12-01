<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App;

use Laminas\Diactoros\Response\JsonResponse;

class ArrayResponse extends JsonResponse
{
    public function __construct()
    {
        parent::__construct([], 200, [], self::DEFAULT_JSON_FLAGS);
    }

    public function addData($value): JsonResponse
    {
        $payload = $this->getPayload();
        if (!is_array($payload)) {
            return $this;
        }
        $payload[] = $value;
        return $this->withPayload($payload);
    }
}
