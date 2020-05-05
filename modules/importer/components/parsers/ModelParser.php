<?php

declare(strict_types=1);

namespace importer\components\parsers;

use yii\db\ActiveRecord;

/**
 * Interface ModelParser
 */
interface ModelParser
{
    /**
     * @param string $filename
     *
     * @param string $modelClass
     *
     * @return ActiveRecord[]|null
     */
    public function parse(string $filename, string $modelClass): ?array;
}
