<?php

declare(strict_types=1);

namespace crud\components\columns;

/**
 * Class ChoiceColumn
 */
class ChoiceColumn extends AbstractColumn
{
    /**
     * @return array|null
     */
    public function getValues(): ?array
    {
        return $this->options['values'] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function validate($value): ?string
    {
        $result = parent::validate($value);

        if ($result !== null) {
            return $this->getValues() === null || in_array($value, $this->getValues()) ? null
                : 'This value is not in choice list';
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getFieldAction(): string
    {
        return 'dropDownList';
    }

    /**
     * @return array
     */
    public function getFieldOptions(): array
    {
        return [
            $this->getValues() ?? [],
        ];
    }
}
