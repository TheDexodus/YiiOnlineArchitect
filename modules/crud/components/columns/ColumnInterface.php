<?php

declare(strict_types=1);

namespace crud\components\columns;

use yii\db\ActiveRecord;

/**
 * Interface ColumnInterface
 */
interface ColumnInterface
{
    /**
     * @return string
     */
    public function getSetter(): ?string;

    /**
     * @param string|null $setter
     */
    public function setSetter(?string $setter): void;

    /**
     * @return array
     */
    public function getOptions(): array;

    /**
     * @param array|string $options
     */
    public function addOptions($options): void;

    /**
     * @param string|null $widget
     */
    public function setWidget(?string $widget): void;

    /**
     * @return string|null
     */
    public function getWidget(): ?string;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string|null
     */
    public function getPattern(): ?string;

    /**
     * @param string|null $pattern
     */
    public function setPattern(?string $pattern): void;

    /**
     * @return array|null
     */
    public function getValidators(): ?array;

    /**
     * @param array|null $validators
     */
    public function setValidators(?array $validators): void;

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function applyPattern($value): bool;

    /**
     * @param mixed $value
     *
     * @return string|null
     */
    public function validate($value): ?string;

    /**
     * @return string
     */
    public function getFieldAction(): string;

    /**
     * @return array
     */
    public function getFieldOptions(): array;

    /**
     * @param ActiveRecord $record
     * @param string       $key
     * @param              $value
     */
    public function afterChange(ActiveRecord $record, string $key, &$value): void;
}
