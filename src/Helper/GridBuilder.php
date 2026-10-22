<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Keeper\Helper;

use Spiral\Toolkit\Helper\Handlebars;

final class GridBuilder
{
    private static $uniqueID = 0;

    /** @var string */
    private $gridID;

    /**
     * Other grid options.
     *
     * @var array
     */
    private $options = [
        'captureForms'   => [],
        'captureFilters' => [],
    ];

    /**
     * Columns definitions.
     *
     * @var array
     */
    private $columns = [];

    /**
     * Cell definitions.
     *
     * @var array
     */
    private $cells = [];

    /**
     * Bulk Actions definitions.
     *
     * @var array
     */
    private $bulkActions = [];

    /**
     * Action definitions.
     *
     * @var array
     */
    private $actions = [];

    /**
     * GridBuilder constructor.
     */
    public function __construct()
    {
        $this->gridID = $this->uniqueID();
    }

    /**
     * @return $this
     */
    public function setID(string $id): self
    {
        $this->gridID = $id;

        return $this;
    }

    public function getID(): string
    {
        return $this->gridID;
    }

    /**
     * @param mixed  $value
     * @return $this
     */
    public function setOption(string $path, $value): self
    {
        /** @psalm-suppress UnsupportedPropertyReferenceUsage */
        $loc = &$this->options;
        foreach (\explode('.', $path) as $step) {
            $loc = &$loc[$step];
        }
        $loc = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function addAction(array $action): self
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * @return $this
     */
    public function addBulkAction(string $id, array $action): self
    {
        $this->bulkActions[$id] = $action;

        return $this;
    }

    /**
     * @return $this
     */
    public function addColumn(
        string $name,
        array $column,
        array $renderer = [],
        ?string $cellClass = null,
    ): self {
        $this->columns[] = ['id' => $name] + $column;

        if ($renderer !== []) {
            $this->cells[$name] = $renderer;
        }

        if ($cellClass !== null) {
            $this->setOption('ui.cellClassName.' . $name, $cellClass);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function captureForm(string $id): self
    {
        $this->options['captureForms'][] = $id;

        return $this;
    }

    /**
     * @return $this
     */
    public function captureFilterForm(string $id): self
    {
        $this->options['captureFilters'][] = $id;

        return $this;
    }

    public function render(): string
    {
        $schema = $this->options;
        unset($schema['actions']);

        $schema['columns'] = $this->columns;
        $schema['renderers']['cells'] = $this->cells;
        $schema['renderers']['actions'] = $this->bulkActions;

        if ($this->actions !== []) {
            $schema['columns'][] = [
                'id'    => 'actions',
                'title' => $this->options['actions']['title'],
            ];

            $schema['renderers']['cells']['actions'] = [
                'name'      => 'actions',
                'arguments' => [
                    'kind'      => $this->options['actions']['kind'],
                    'size'      => $this->options['actions']['size'],
                    'className' => $this->options['actions']['class'],
                    'icon'      => $this->options['actions']['icon'],
                    'label'     => $this->options['actions']['label'],
                    'actions'   => $this->actions,
                ],
            ];
        }

        return \json_encode($schema);
    }

    /**
     * Convert {opt} statements to {{opt}} statements.
     *
     */
    public function toHandlebars(?string $template): ?string
    {
        return Handlebars::convert($template);
    }

    public function uniqueID(): string
    {
        return 'g-' . \crc32((string) (self::$uniqueID++));
    }
}
