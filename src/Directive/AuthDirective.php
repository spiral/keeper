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
use Spiral\Stempler\Directive\RouteDirective;
use Spiral\Stempler\Node\Dynamic\Directive;

class AuthDirective extends AbstractDirective
{
    use ArrayTrait;

    /** @var RouteDirective */
    private $route;

    public function __construct(RouteDirective $route)
    {
        parent::__construct();
        $this->route = $route;
    }

    /**
     * @param Directive $directive
     * @return string
     */
    public function renderAuth(Directive $directive): string
    {
        return \sprintf(
            '<?php if($this->container->get(\Spiral\Security\GuardInterface::class)->allows(%s)): ?>',
            $directive->body,
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

    public function renderLogout(Directive $directive): string
    {
        $token = '\'token\' => !$this->container->has(\Spiral\Auth\AuthContextInterface::class) ? null : '
            . '($this->container->get(\Spiral\Auth\AuthContextInterface::class)->getToken()->getID() ?? null)';

        if (isset($directive->values[1]) && $this->endsWithArray($directive->values[1])) {
            $directive->values[1] = $this->appendToArray($directive->values[1], $token);
            $directive->body = \implode(', ', $directive->values);
        } else {
            $directive->body .= ", [$token]";
            $directive->values[] = "[$token]";
        }

        return $this->route->renderRoute($directive);
    }

    private function appendToArray(string $value, string $postfix): string
    {
        return $value === '[]' ? "[$postfix]" : \rtrim($value, ']') . ", $postfix]";
    }
}
