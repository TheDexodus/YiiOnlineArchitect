<?php

declare(strict_types=1);

namespace crud\components\columns;

/**
 * Class AbstractRelationColumn
 */
abstract class AbstractRelationColumn extends AbstractColumn implements RelationColumnInterface
{
    /**
     * @var string
     */
    protected $targetClass;

    /**
     * @var AbstractColumn[]
     */
    protected $columns;

    /**
     * @var string
     */
    protected $column_display;

    /**
     * @inheritDoc
     */
    public function getTargetClass(): string
    {
        return $this->targetClass;
    }

    /**
     * @param string $targetClass
     */
    public function setTargetClass(string $targetClass): void
    {
        $this->targetClass = $targetClass;
    }

    /**
     * @inheritDoc
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @inheritDoc
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }

    /**
     * @return string|null
     */
    public function getColumnDisplay(): ?string
    {
        return $this->column_display;
    }

    /**
     * @param string|null $column_display
     */
    public function setColumnDisplay(?string $column_display): void
    {
        $this->column_display = $column_display;
    }
}
