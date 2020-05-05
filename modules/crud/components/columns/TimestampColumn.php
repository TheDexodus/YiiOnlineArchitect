<?php

declare(strict_types=1);

namespace crud\components\columns;

/**
 * Class TimestampColumn
 */
class TimestampColumn extends AbstractColumn
{
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
            'datetime-local',
        ];
    }
}
