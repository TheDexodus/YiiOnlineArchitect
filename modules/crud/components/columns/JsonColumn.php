<?php

declare(strict_types=1);

namespace crud\components\columns;

use slavkovrn\jsoneditor\JsonEditorWidget;

/**
 * Class JsonColumn
 */
class JsonColumn extends TextColumn
{
    /**
     * @inheritDoc
     */
    public function setWidget(?string $widget): void
    {
        $this->widget = $widget ?? JsonEditorWidget::class;
    }

    /**
     * @inheritDoc
     */
    public function getFieldOptions(): array
    {
        return ['rootNodeName' => 'Details'];
    }
}
