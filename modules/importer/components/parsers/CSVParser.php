<?php

declare(strict_types=1);

namespace importer\components\parsers;

use app\models\ImportableInterface;
use Exception;
use yii\db\ActiveRecord;

/**
 * Class CSVParser
 */
class CSVParser implements ModelParser
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function parse(string $filename, string $modelClass): ?array
    {
        $contentParts = explode("\n", file_get_contents($filename));

        if (!new $modelClass() instanceof ImportableInterface) {
            return null;
        }

        if (count($contentParts) < 2) {
            return null;
        }

        $columns = explode(';', array_shift($contentParts));
        $models = [];

        foreach ($contentParts as $part) {
            if ($part === '') {
                continue;
            }

            /** @var ImportableInterface|ActiveRecord $model */
            $model = new $modelClass();
            $condition = [];
            $relations = [];

            $values = explode(';', $part);

            foreach ($columns as $columnIdx => $column) {
                if (isset($model->getRelationMaps()[$column])) {
                    $relationMap = $model->getRelationMaps()[$column];
                    $relationClass = $relationMap['class'];

                    $relationRecord = $relationClass::findOne(
                        [$relationMap['target_column'] => $values[$columnIdx]]
                    );

                    if (!$relationRecord instanceof $relationClass) {
                        throw new Exception(
                            sprintf(
                                'Model "%s" with column "%s" not founded',
                                $modelClass,
                                $relationMap['target_column']
                            )
                        );
                    }

                    $relations[$relationMap['attribute']] = $relationRecord;
                    //$condition[$column] = $values[$columnIdx];

                    continue;
                }

                $model->$column = $values[$columnIdx];
                if ($modelClass::getTableSchema()->columns[$column]->type === 'json') {
                    $condition[$column] = '"'.$values[$columnIdx].'"';
                } else {
                    $condition[$column] = $values[$columnIdx];
                }
            }

            $oldRecord = $modelClass::findOne($condition);

            if ($oldRecord instanceof $modelClass) {
                continue;
            }

            foreach ($relations as $relationAttribute => $relation) {
                $model->link($relationAttribute, $relation);
            }

            $model->save();
            $models[] = $model;
        }

        return $models;
    }
}
