<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Module\Sitemap;

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
     * @param string $name
     * @param array  $options
     * @param array  $nodes
     */
    public function __construct(string $name, array $options = [], array $nodes = [])
    {
        $this->name = $name;
        $this->options = $options;
        $this->nodes = $nodes;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $option
     * @return bool
     */
    public function hasOption(string $option): bool
    {
        return array_key_exists($option, $this->options);
    }

    /**
     * @param string $option
     * @return mixed|null
     */
    public function getOption(string $option)
    {
        return $this->options[$option] ?? null;
    }

    /**
     * @param string $option
     * @param mixed  $value
     */
    public function setOption(string $option, $value): void
    {
        $this->options[$option] = $value;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param Node $node
     */
    public function add(Node $node): void
    {
        $this->nodes[$node->name] = $node;
    }

    /**
     * @param string $name
     * @return Node|null
     */
    public function get(string $name): ?Node
    {
        return $this->nodes[$name] ?? null;
    }

    /**
     * @return iterable
     */
    public function getIterator(): iterable
    {
        yield from $this->nodes;
    }
}
