<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Module;

use Spiral\Keeper\Exception\SitemapException;
use Spiral\Keeper\Module\Sitemap\Node;
use Spiral\Security\GuardInterface;

/**
 * @method Sitemap|null group(string $name, string $title = null, array $options = [])
 * @method Sitemap|null segment(string $name, string $title = null, array $options = [])
 * @method Sitemap|null link(string $name, string $title = null, array $options = [])
 * @method Sitemap|null view(string $name, string $title = null, array $options = [])
 */
final class Sitemap implements \IteratorAggregate
{
    public const TYPE_ROOT    = 'root';
    public const TYPE_SEGMENT = 'segment';
    public const TYPE_GROUP   = 'group';
    public const TYPE_LINK    = 'link';
    public const TYPE_VIEW    = 'view';

    /** @var string */
    private $namespace;

    /** @var Node */
    private $root;

    /**
     * @param string $namespace
     */
    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
        $this->root = new Node('root', ['type' => self::TYPE_ROOT]);
    }

    /**
     * Declare or create node.
     *
     * @param string $name
     * @param array  $param
     * @return Sitemap|null
     */
    public function __call(string $name, array $param)
    {
        if (!in_array($name, [self::TYPE_GROUP, self::TYPE_SEGMENT, self::TYPE_LINK, self::TYPE_VIEW], true)) {
            throw new SitemapException("Undefined method `{$name}`");
        }

        if (count($param) === 0) {
            throw new SitemapException('Node name required and must be string');
        }

        if ($this->root->get($param[0]) !== null) {
            $node = $this->root->get($param[0]);
            if ($node === null) {
                return null;
            }

            // found chain
            return $this->withRoot($node);
        }

        $args = ['type' => $name, 'title' => $param[1]];
        if (isset($param[2]) && is_array($param[2])) {
            $args = array_merge($param [2], $args);
        }

        $node = new Node($param[0], $args);
        $this->root->add($node);

        return $this->withRoot($node);
    }

    /**
     * Find all visible nodes and highlight current path.
     *
     * @param GuardInterface $guard
     * @param string|null    $targetNode
     * @return Sitemap|null
     */
    public function withVisibleNodes(GuardInterface $guard, string $targetNode = null): ?Sitemap
    {
        $sitemap = clone $this;
        $sitemap->root = $this->filterVisible($this->root, $guard, $targetNode);

        if ($sitemap->root === null) {
            return null;
        }

        return $sitemap;
    }

    /**
     * @return Node[]
     */
    public function getActivePath(): array
    {
        $result = [];
        foreach ($this->findActiveNode($this->root) as $node) {
            if ($node->getOption('type') === self::TYPE_ROOT) {
                continue;
            }

            $result[] = $node;
        }

        return $result;
    }

    /**
     * @return \Generator
     */
    public function getIterator(): \Generator
    {
        yield from $this->root;
    }

    /**
     * @param Node $node
     * @return \Generator|Node[]
     */
    private function findActiveNode(Node $node): \Generator
    {
        if (!$node->getOption('active')) {
            return;
        }

        yield $node;

        foreach ($node as $child) {
            yield from $this->findActiveNode($child);
        }
    }

    /**
     * @param Node           $node
     * @param GuardInterface $guard
     * @param string|null    $targetNode
     * @return Node|null
     */
    private function filterVisible(Node $node, GuardInterface $guard, string $targetNode = null): ?Node
    {
        if ($node->hasOption('permission')) {
            if (!$guard->allows($this->namespace . '.' . $node->getOption('permission'))) {
                return new Node('empty');
            }
        }

        $activePath = false;
        $nodes = [];
        foreach ($node as $name => $child) {
            $child = $this->filterVisible($child, $guard, $targetNode);
            if ($child !== null) {
                $nodes[$child->getName()] = $child;

                if ($child->getOption('active') === true) {
                    $activePath = true;
                }
            }
        }

        if ($nodes === [] && !in_array($node->getOption('type'), [self::TYPE_LINK, self::TYPE_VIEW], true)) {
            return new Node('empty');
        }

        $opts = $node->getOptions();
        if ($node->getName() === $targetNode || $activePath) {
            $opts['active'] = true;
        }

        return new Node($node->getName(), $opts, $nodes);
    }

    /**
     * @param Node $root
     * @return $this
     */
    private function withRoot(Node $root): self
    {
        $sitemap = clone $this;
        $sitemap->root = $root;

        return $sitemap;
    }
}