<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Annotation;

use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(namespace="annotation", name="names")
 */
class NameController
{
    /**
     * @Action(route="/without")
     * @return string
     */
    public function withoutName(): string
    {
        return 'name: without name';
    }

    /**
     * @Action(route="with", name="with:name")
     * @return string
     */
    public function withName(): string
    {
        return 'name: with name';
    }
}
