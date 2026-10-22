<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Module\Sitemap;

/**
 * @implements \IteratorAggregate<string, Node>
 */
class Node implements \IteratorAggregate
{
    /** @var string */
    private $name;

    /** @var array */
    private $options;

    /** @var Node[] */
    private $nodes;

    /**
     * Node constructor.
     *
     */
    public function __construct(string $name, array $options = [], array $nodes = [])
    {
        $this->name = $name;
        $this->options = $options;
        $this->nodes = $nodes;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function hasOption(string $option): bool
    {
        return \array_key_exists($option, $this->options);
    }

    /**
     * @return mixed|null
     */
    public function getOption(string $option)
    {
        return $this->options[$option] ?? null;
    }

    /**
     * @param mixed  $value
     */
    public function setOption(string $option, $value): void
    {
        $this->options[$option] = $value;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function add(Node $node): void
    {
        $this->nodes[$node->name] = $node;
    }

    public function get(string $name): ?Node
    {
        return $this->nodes[$name] ?? null;
    }

    /**
     * @return iterable|Node[]
     */
    public function getIterator(): \Traversable
    {
        $positions = [];
        $position = 0;
        $nodes = $this->nodes;
        foreach ($this->nodes as $name => $node) {
            $currentPosition = $node->getOption('position') ?? $position;
            if ($node->getOption('position') === null) {
                $position++;
            }

            $positions[$name] = $currentPosition;
            $node->setOption('position', $currentPosition);
        }
        \array_multisort($positions, SORT_ASC, $nodes);

        yield from $nodes;
    }

    public function getElements(): array
    {
        $elements = [];
        $nested = [];
        foreach ($this->nodes as $element => $child) {
            $elements[] = $element;
            $nestedElements = $child->getElements();
            if ($nestedElements) {
                $nested[] = $child->getElements();
            }
        }

        return $nested ? \array_merge($elements, ...$nested) : $elements;
    }
}
