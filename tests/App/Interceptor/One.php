<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\App\Interceptor;

use Spiral\Core\CoreInterceptorInterface;
use Spiral\Core\CoreInterface;

class One implements CoreInterceptorInterface
{
    public function process(string $controller, string $action, array $parameters, CoreInterface $core)
    {
        $result = $core->callAction($controller, $action, $parameters);
        if (!is_array($result)) {
            $result = [];
        }
        $result[] = 'one';
        return $result;
    }
}
