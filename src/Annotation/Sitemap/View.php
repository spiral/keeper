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
 * @Target({"METHOD"})
 */
final class View
{
    /**
     * @Attribute(name="name", type="string", required=true)
     * @var string
     */
    public $parent;

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
