<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper;

use Nyholm\Psr7\Response;

class NotFoundResponse extends Response
{
    public function __construct()
    {
        parent::__construct(status: 404);
    }
}
