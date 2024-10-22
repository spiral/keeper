<?php

declare(strict_types=1);

namespace Spiral\Keeper\Directive;

trait ArrayTrait
{
    private function endsWithArray($value): bool
    {
        if (!\is_string($value)) {
            return false;
        }

        return \mb_strpos($value, 'inject') === 0 || \mb_substr($value, \mb_strlen($value) - 1, 1) === ']';
    }
}
