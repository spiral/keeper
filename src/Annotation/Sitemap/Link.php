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
final class Link
{
    /**
     * @Attribute(name="name", type="string")
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

    public function hasRelativeParent(): bool
    {
        if ($this->parent && is_string($this->parent)) {
            $parent = trim($this->parent, ' .');
            return $parent && mb_strpos($parent, '.') === false;
        }
        return false;
    }

    public function hasAbsoluteParent(): bool
    {
        if ($this->parent && is_string($this->parent)) {
            $parent = trim($this->parent, ' .');
            return $parent && mb_strpos($parent, '.') !== false;
        }
        return false;
    }
}
