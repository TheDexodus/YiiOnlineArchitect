<?php

declare(strict_types=1);

namespace importer\components\parsers;

use app\models\ImportableInterface;
use Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use yii\db\ActiveRecord;

/**
 * Class XLSParser
 */
class XLSParser implements ModelParser
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function parse(string $filename, string $modelClass): ?array
    {
        if (!new $modelClass() instanceof ImportableInterface) {
            return null;
        }

        $reader = new Xls();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();

        $columns = [];
        $models = [];
        $i = 0;

        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();

            if ($i === 0) {
                foreach ($cellIterator as $idx => $cell) {
                    $columns[$idx] = $cell->getValue();
                }

                $i++;
            } else {
                /** @var ImportableInterface|ActiveRecord $model */
                $model = new $modelClass();
                $condition = [];
                $relations = [];

                foreach ($cellIterator as $idx => $cell) {
                    $value = $cell->getValue();
                    $columnName = $columns[$idx];

                    if (isset($model->getRelationMaps()[$columnName])) {
                        $relationMap = $model->getRelationMaps()[$columnName];
                        $relationClass = $relationMap['class'];

                        $relationRecord = $relationClass::findOne(
                            [$relationMap['target_column'] => $value]
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
                    if ($modelClass::getTableSchema()->columns[$columnName]->type === 'json') {
                        if ($value === null) {
                            $value = '{}';
                        }
                        $model->$columnName = $value;
                        $condition[$columnName] = '"'.$value.'"';
                    } else {
                        $condition[$columnName] = $value;
                        $model->$columnName = $value;

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
        }

        return $models;
    }
}
