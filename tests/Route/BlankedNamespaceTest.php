<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Route;

use Spiral\Tests\Keeper\HttpTrait;

class BlankedNamespaceTest extends NamespaceTest
{
    use HttpTrait;

    protected const NAMESPACE = 'blank';
    protected const PREFIX    = '';
}
