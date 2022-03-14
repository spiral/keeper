<?php

declare(strict_types=1);

namespace Spiral\Keeper\Helper;

use Spiral\Attributes\ReaderInterface;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;
use Spiral\Tokenizer\ClassesInterface;

/**
 * @internal
 */
final class Locator
{
    /** @var ClassesInterface */
    private $locator;
    /** @var ReaderInterface */
    private $reader;

    public function __construct(ClassesInterface $locator, ReaderInterface $reader)
    {
        $this->reader = $reader;
        $this->locator = $locator;
    }

    public function locateNamespaceControllers(string $namespace): iterable
    {
        $matches = [];
        foreach ($this->locator->getClasses() as $class) {
            $controller = $this->reader->firstClassMetadata($class, Controller::class);
            if ($controller === null || $controller->namespace !== $namespace) {
                continue;
            }

            $matches[$class->getFileName()] = [$class, $controller];
        }

        ksort($matches);
        foreach ($matches as $match) {
            yield $match[0] => $match[1];
        }
    }

    public function locateMethodsWithAction(\ReflectionClass $class): iterable
    {
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $action = $this->reader->firstFunctionMetadata($method, Action::class);
            if (!$action instanceof Action) {
                continue;
            }

            yield $method => $action;
        }
    }
}
