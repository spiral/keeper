<?php

declare(strict_types=1);

namespace Spiral\Keeper\Annotation;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS"})
 * @Attributes({
 *     @Attribute("name", required=true, type="string"),
 *     @Attribute("prefix", type="string"),
 *     @Attribute("namespace", type="string"),
 *     @Attribute("defaultAction", type="string")
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class Controller
{
    /**
     * @type string
     */
    public $name;

    /**
     * @type string
     */
    public $prefix;

    /**
     * @type string|null
     */
    public $namespace = 'keeper';

    /**
     * @type string|null
     */
    public $defaultAction;

    public function __construct(
        string $name,
        ?string $prefix = null,
        string $namespace = 'keeper',
        ?string $defaultAction = null,
    ) {
        $this->name = $name;
        $this->prefix = $prefix;
        $this->namespace = $namespace;
        $this->defaultAction = $defaultAction;
    }
}
