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
use Spiral\Stempler\Node\Dynamic\Directive;

class AuthDirective extends AbstractDirective
{
    /**
     * @param Directive $directive
     * @return string
     */
    public function renderAuth(Directive $directive): string
    {
        return sprintf(
            '<?php if($this->container->get(\Spiral\Security\GuardInterface::class)->allows(%s)): ?>',
            $directive->body
        );
    }


    /**
     * @param Directive $directive
     * @return string
     */
    public function renderEndAuth(Directive $directive): string
    {
        return '<?php endif; ?>';
    }
}
