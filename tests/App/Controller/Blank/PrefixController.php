<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Blank;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(namespace="blank", name="prefix", prefix="/pref/ix_")
 */
class PrefixController
{
    /**
     * @Action(route="/without")
     * @return string
     */
    public function withoutName(): string
    {
        return 'prefix: without name';
    }

    /**
     * @Action(route="with", name="with:prefix:name")
     * @return string
     */
    public function withName(): string
    {
        return 'prefix: with name';
    }
}
