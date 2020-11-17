<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper\Route;

use Spiral\Tests\Keeper\HttpTrait;

class PrefixedNamespaceTest extends NamespaceTest
{
    use HttpTrait;

    protected const NAMESPACE = 'annotation';
    protected const PREFIX    = '/annotation_';
}
