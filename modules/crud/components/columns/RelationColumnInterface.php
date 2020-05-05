<?php

declare(strict_types=1);

namespace crud\components\columns;

use yii\db\ActiveRecord;

/**
 * Interface RelationColumnInterface
 */
interface RelationColumnInterface
{
    /**
     * @return string
     */
    public function getTargetClass(): string;

    /**
     * @param string $targetClass
     *
     * @return void
     */
    public function setTargetClass(string $targetClass): void;

    /**
     * @return ColumnInterface[]
     */
    public function getColumns(): array;

    /**
     * @param ColumnInterface[] $columns
     */
    public function setColumns(array $columns): void;

    /**
     * @return string|null
     */
    public function getColumnDisplay(): ?string;

    /**
     * @param string|null $column_display
     */
    public function setColumnDisplay(?string $column_display): void;
}
