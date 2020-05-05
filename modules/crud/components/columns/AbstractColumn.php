<?php

declare(strict_types=1);

namespace crud\components\columns;

use yii\db\ActiveRecord;
use yii\validators\Validator;

/**
 * Class AbstractColumn
 */
abstract class AbstractColumn implements ColumnInterface
{
    /** @var string|null */
    protected $pattern = null;

    /** @var array|null */
    protected $validators = null;

    /** @var string|null */
    protected $widget = null;

    /** @var string|null */
    protected $setter = null;

    /** @var array */
    protected $options = [];

    /**
     * @inheritDoc
     */
    public function afterChange(ActiveRecord $record, string $key, &$value): void
    {
        return;
    }

    /**
     * @inheritDoc
     */
    public function getSetter(): ?string
    {
        return $this->setter;
    }

    /**
     * @inheritDoc
     */
    public function setSetter(?string $setter): void
    {
        $this->setter = $setter;
    }

    /**
     * @inheritDoc
     */
    public function getWidget(): ?string
    {
        return $this->widget;
    }

    /**
     * @inheritDoc
     */
    public function setWidget(?string $widget): void
    {
        $this->widget = $widget;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public function addOptions($options): void
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        $parts = explode('\\', static::class);

        return str_replace('Column', '', $parts[count($parts) - 1]);
    }

    /**
     * @inheritDoc
     */
    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    /**
     * @inheritDoc
     */
    public function setPattern(?string $pattern): void
    {
        $this->pattern = $pattern;
    }

    /**
     * @inheritDoc
     */
    public function getValidators(): ?array
    {
        return $this->validators ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setValidators(?array $validators): void
    {
        $this->validators = $validators;
    }

    /**
     * @inheritDoc
     */
    public function applyPattern($value): bool
    {
        return $this->getPattern() === null || preg_match('/'.$this->getPattern().'/', $value) === 1;
    }

    /**
     * @inheritDoc
     */
    public function validate($value): ?string
    {
        if (!$this->applyPattern($value)) {
            return 'Invalid format';
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getFieldAction(): string
    {
        return 'textInput';
    }

    /**
     * @inheritDoc
     */
    public function getFieldOptions(): array
    {
        return [];
    }
}
