<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Annotation;

use Spiral\Router\Route;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
final class Action
{
    /**
     * @Attribute(name="route", type="string", required=true)
     * @var string
     */
    public $route;

    /**
     * @Attribute(name="verbs", type="mixed", required=true)
     * @var mixed
     */
    public $methods = Route::VERBS;
}
