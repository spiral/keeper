<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Annotation\Sitemap;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Spiral\Keeper\Module\Sitemap\Method;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
final class View
{
    use ParentTrait;

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

    /**
     * @Attribute(name="position", type="float")
     * @var float
     */
    public $position;

    public function getOptions(Method $method = null): array
    {
        $options = $this->options + ['position' => $this->position];
        if ($method !== null) {
            $options['route'] = $method->route;
        }

        return $options;
    }
}
