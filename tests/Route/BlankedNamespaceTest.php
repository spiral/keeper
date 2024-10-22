<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Route;

use Spiral\Tests\Keeper\HttpTrait;

class BlankedNamespaceTest extends NamespaceTestBase
{
    use HttpTrait;

    protected const NAMESPACE = 'blank';
    protected const PREFIX    = '';
}
