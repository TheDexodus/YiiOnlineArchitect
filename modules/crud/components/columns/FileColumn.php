<?php

declare(strict_types=1);

namespace crud\components\columns;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * Class FileColumn
 */
class FileColumn extends AbstractColumn
{
    /**
     * @inheritDoc
     */
    public function getFieldAction(): string
    {
        return 'fileInput';
    }

    /**
     * @inheritDoc
     */
    public function afterChange(ActiveRecord $record, string $key, &$value): void
    {
        if ($value instanceof UploadedFile) {
            $randomName = sha1(microtime()) . '.' . $value->extension;
            /** @var UploadedFile $value */
            $value->name = $randomName;

            $value->saveAs(Yii::getAlias($this->options['save_path'] . $randomName));
        }
    }
}
