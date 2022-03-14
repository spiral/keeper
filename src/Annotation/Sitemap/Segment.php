<?php

declare(strict_types=1);

namespace Spiral\Keeper\Annotation\Sitemap;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS"})
 * @Attributes({
 *     @Attribute("parent", type="string"),
 *     @Attribute("name", required=true, type="string"),
 *     @Attribute("title", required=true, type="string"),
 *     @Attribute("options", type="array"),
 *     @Attribute("position", type="float")
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class Segment
{
    /**
     * @var string
     */
    public $parent = 'root';

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $title;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var float
     */
    public $position;

    public function __construct(
        string $name,
        string $title,
        string $parent = 'root',
        array $options = [],
        ?float $position = null
    ) {
        $this->name = $name;
        $this->title = $title;
        $this->parent = $parent;
        $this->options = $options;
        $this->position = $position;
    }
}
