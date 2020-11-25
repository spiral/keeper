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
    use ArrayTrait;

    public function renderKeeper(Directive $directive): string
    {
        return $this->doRender($directive, 'keeper');
    }

    /**
     * @param Directive $directive
     * @return string
     * @deprecated use @keeper(namespace, name, args) instead
     */
    public function renderAction(Directive $directive): string
    {
        $directive = $this->handleLegacyDirective($directive);
        return $this->doRender($directive, 'action');
    }

    private function doRender(Directive $directive, string $name): string
    {
        if (count($directive->values) < 2) {
            throw new DirectiveException(
                "Unable to call @$name directive, at least 2 values is required",
                $directive->getContext()
            );
        }

        return sprintf(
            '<?php echo $this->container->get(\Spiral\Keeper\Helper\RouteBuilder::class)->uri(%s); ?>',
            $directive->body
        );
    }


    private function handleLegacyDirective(Directive $directive): Directive
    {
        if (count($directive->values) < 1) {
            throw new DirectiveException(
                'Unable to call @route directive, at least 1 value is required',
                $directive->getContext()
            );
        }

        $count = count($directive->values);
        if ($count === 1 || ($count === 2 && $this->endsWithArray($directive->values[1]))) {
            array_unshift($directive->values, "'keeper'");
            $directive->body = "'keeper', {$directive->body}";
            return $directive;
        }

        return $directive;
    }
}
