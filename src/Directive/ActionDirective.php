<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Directive;

use Spiral\Stempler\Directive\AbstractDirective;
use Spiral\Stempler\Exception\DirectiveException;
use Spiral\Stempler\Node\Dynamic\Directive;

class ActionDirective extends AbstractDirective
{
    /**
     * @param Directive $directive
     * @return string
     */
    public function renderAction(Directive $directive): string
    {
        if (count($directive->values) < 1) {
            throw new DirectiveException(
                'Unable to call @route directive, at least 1 value is required',
                $directive->getContext()
            );
        }

        return sprintf(
            '<?php echo $this->container->get(\Spiral\Keeper\Module\RouteRegistry::class)->uri(%s); ?>',
            $directive->body
        );
    }
}
