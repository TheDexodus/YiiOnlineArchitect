<?php

declare(strict_types=1);

namespace crud\components\columns;

/**
 * Class ColorColumn
 */
class ColorColumn extends AbstractColumn
{
    /**
     * @inheritDoc
     */
    public function getPattern(): ?string
    {
        return $this->pattern ?? '^#[0-9ABCDEFabcdef]{6}$';
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
            'color',
        ];
    }
}
