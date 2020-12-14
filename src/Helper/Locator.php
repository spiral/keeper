<?php

declare(strict_types=1);

namespace Spiral\Keeper\Helper;

use Doctrine\Common\Annotations\AnnotationReader;
use Spiral\Annotations\AnnotationLocator;
use Spiral\Keeper\Annotation\Action;
use Spiral\Keeper\Annotation\Controller;

/**
 * @internal
 */
final class Locator
{
    /** @var AnnotationLocator */
    private $locator;
    /** @var AnnotationReader */
    private $reader;

    public function __construct(AnnotationLocator $locator, AnnotationReader $reader)
    {
        $this->reader = $reader;
        $this->locator = $locator;
    }

    public function locateNamespaceControllers(string $namespace): iterable
    {
        $matches = [];
        foreach ($this->locator->findClasses(Controller::class) as $match) {
            /** @var Controller $controller */
            $controller = $match->getAnnotation();
            if ($controller->namespace !== $namespace) {
                continue;
            }
            $matches[$match->getClass()->getFileName()] = $match;
        }

        ksort($matches);
        foreach ($matches as $match) {
            yield $match->getClass() => $match->getAnnotation();
        }
    }

    public function locateMethodsWithAction(\ReflectionClass $class): iterable
    {
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $action = $this->reader->getMethodAnnotation($method, Action::class);
            if (!$action instanceof Action) {
                continue;
            }

            yield $method => $action;
        }
    }
}
