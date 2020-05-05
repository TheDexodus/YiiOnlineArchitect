<?php

declare(strict_types=1);

namespace crud\components;

use crud\components\columns\AbstractColumn;
use crud\components\columns\ColumnInterface;
use crud\components\columns\HasOneColumn;
use crud\components\columns\RelationColumnInterface;
use yii\db\ActiveQuery;

/**
 * Class Model
 */
class Model
{
    public const ACTION_VIEW   = 'view';
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';

    /** @var string */
    public $title = 'Model';

    /** @var string */
    public $name;

    /** @var string */
    public $class;

    /** @var array */
    public $actions = [self::ACTION_VIEW, self::ACTION_CREATE, self::ACTION_UPDATE, self::ACTION_DELETE];

    /** @var array|AbstractColumn[] */
    public $columns = [];

    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct(string $name, array $options = [])
    {
        $options['name'] = $name;
        $this->applyOptions($options);
    }

    /**
     * @param array $options
     */
    public function applyOptions(array $options): void
    {
        $this->class = $options['class'];
        $this->name = $options['name'];
        $this->title = $options['title'] ?? $this->title;
        $this->actions = $options['actions'] ?? $this->actions;
        $this->columns = $this->parseColumns($options['columns']);
    }

    /**
     * @param array $columns
     *
     * @return AbstractColumn[]
     */
    private function parseColumns(array $columns): array
    {
        $result = [];

        foreach ($columns as $columnName => $columnOptions) {
            $columnClass = 'crud\components\columns\\'.ucfirst($columnOptions['type']).'Column';

            $column = new $columnClass();

            if ($column instanceof RelationColumnInterface) {
                $column->setTargetClass($this->getColumnOption($columnOptions, 'class'));
                $column->setColumns($this->parseColumns($this->getColumnOption($columnOptions, 'columns')));
                $column->setColumnDisplay($this->getColumnOption($columnOptions, 'column_display'));
                if ($column instanceof HasOneColumn) {
                    $column->setMethod($this->getColumnOption($columnOptions, 'method', HasOneColumn::METHOD_CREATE));
                }
            } elseif ($column instanceof ColumnInterface) {
                $column->setPattern($this->getColumnOption($columnOptions, 'pattern'));
                $column->setValidators($this->getColumnOption($columnOptions, 'validators'));
                $column->setWidget($this->getColumnOption($columnOptions, 'widget'));
                $column->setSetter($this->getColumnOption($columnOptions, 'setter'));
            } else {
                continue;
            }
            $column->addOptions($columnOptions);

            $result[$columnName] = $column;
        }

        return $result;
    }

    /**
     * @param array  $options
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getColumnOption(array &$options, string $name, $default = null)
    {
        $result = $options[$name] ?? $default;

        unset($options[$name]);

        return $result;
    }

    /**
     * @param string $action
     *
     * @return bool
     */
    public function canAction(string $action): bool
    {
        return in_array($action, $this->actions);
    }

    /**
     * @param mixed $condition
     *
     * @return ActiveQuery
     */
    public function getFillData($condition = []): ActiveQuery
    {
        return $this->class::find($condition);
    }

    /**
     * @param mixed $condition
     *
     * @return mixed
     */
    public function getOneFillData($condition = [])
    {
        return $this->class::findOne($condition);
    }
}
