<?php

declare(strict_types=1);

namespace crud\components\columns;

/**
 * Class NumberColumn
 */
class NumberColumn extends AbstractColumn
{
    /**
     * @return float|null
     */
    public function getMin(): ?float
    {
        return $this->options['min'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getMax(): ?float
    {
        return $this->options['max'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getFieldAction(): string
    {
        return 'input';
    }

    /**
     * @inheritDoc
     */
    public function getFieldOptions(): array
    {
        return [
            'number',
            [
                'step' => $this->options['step'] ?? 1,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function validate($value): ?string
    {
        $result = parent::validate($value);

        if (!is_numeric($value)) {
            return 'Number type have letters';
        }

        if ($result === null) {
            if ($this->getMin() !== null && $value < $this->getMin()) {
                return sprintf('The number is less than the minimum allowed value(%s)', $this->getMin());
            }

            if ($this->getMax() !== null && $value > $this->getMax()) {
                return sprintf('The number is greater than the maximum allowed value(%s)', $this->getMax());
            }

            return null;
        }

        return $result;
    }
}
