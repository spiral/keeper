<?php

declare(strict_types=1);

namespace Spiral\Keeper\Annotation\Sitemap;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;
use Spiral\Attributes\NamedArgumentConstructor;
use Spiral\Keeper\Module\Sitemap\Method;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"METHOD"})
 * @Attributes({
 *     @Attribute("parent", type="string"),
 *     @Attribute("permission", type="string"),
 *     @Attribute("title", required=true, type="string"),
 *     @Attribute("options", type="array"),
 *     @Attribute("position", type="float")
 * })
 */
#[\Attribute(\Attribute::TARGET_METHOD), NamedArgumentConstructor]
final class Link
{
    use ParentTrait;

    /**
     * @var string
     */
    public $parent;

    /**
     * @var string
     */
    public $permission;

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
        string $title,
        ?string $parent = null,
        ?string $permission = null,
        array $options = [],
        ?float $position = null,
    ) {
        $this->parent = $parent;
        $this->title = $title;
        $this->permission = $permission;
        $this->options = $options;
        $this->position = $position;
    }

    public function getOptions(Method $method = null): array
    {
        $options = $this->options + ['position' => $this->position];
        if ($method !== null) {
            $options['route'] = $method->route;
            $options['permission'] = $method->permission;
        }

        return $options;
    }
}
