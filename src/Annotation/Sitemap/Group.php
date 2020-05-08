<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Annotation\Sitemap;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
final class Group
{
    /**
     * @Attribute(name="name", type="string", required=true)
     * @var string
     */
    public $parent = 'root';

    /**
     * @Attribute(name="name", type="string", required=true)
     * @var string
     */
    public $name;

    /**
     * @Attribute(name="title", type="string", required=true)
     * @var string
     */
    public $title;

    /**
     * @Attribute(name="options", type="array")
     * @var array
     */
    public $options = [];
}
