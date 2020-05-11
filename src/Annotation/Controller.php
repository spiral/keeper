<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Annotation;

use Doctrine\Common\Annotations\Annotation\Attribute;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
final class Controller
{
    /**
     * @Attribute(name="name", type="string", required=true)
     * @type string
     */
    public $name;

    /**
     * @Attribute(name="prefix", type="string")
     * @type string
     */
    public $prefix;

    /**
     * @Attribute(name="name", type="string")
     * @type string|null
     */
    public $namespace = 'keeper';
}
