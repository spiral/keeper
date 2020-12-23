<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Controller\Guarded;

use Spiral\Domain\Annotation\Guarded;
use Spiral\Domain\Annotation\GuardNamespace;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @Controller(
 *     name="provided",
 *     prefix="/provided",
 *     namespace="guarded"
 * )
 * @GuardNamespace(namespace="guarded.provided")
 */
class ProvidedController
{
    /**
     * @Guarded(permission="allowed")
     * @Action(route="/allowed")
     */
    public function allowed(): void
    {
    }

    /**
     * @Guarded(permission="forbidden")
     * @Action(route="/forbidden")
     */
    public function forbidden(): void
    {
    }
}
