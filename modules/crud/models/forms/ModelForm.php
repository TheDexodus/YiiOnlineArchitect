<?php

declare(strict_types=1);

namespace crud\models\forms;

use crud\components\columns\ColumnInterface;
use crud\components\columns\FileColumn;
use crud\components\columns\HasManyColumn;
use crud\components\columns\HasOneColumn;
use crud\components\columns\RelationColumnInterface;
use crud\components\helpers\NameHelper;
use crud\components\Model as ModelComponent;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * Class ModelForm
 */
class ModelForm extends Model
{
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_UPDATE = 'update';

    private const ATTRIBUTE_PREFIX          = 'model_';
    private const ATTRIBUTE_RELATIVE_PREFIX = 'relative_';

    public $asd;

    /** @var ModelComponent */
    private $_model;

    /** @var ColumnInterface[] */
    private $_columns;

    /** @var array */
    private $_relations = [];

    /** @var array */
    private $_countInRelations = [];

    /** @var ActiveRecord */
    private $_record = null;

    public function __set($name, $value)
    {
        if (stripos($name, self::ATTRIBUTE_PREFIX) === 0) {
            $this->$name = $value;

            return;
        }

        parent::__set($name, $value);
    }

    /**
     * ModelForm constructor.
     *
     * @param ModelComponent    $model
     * @param array             $config
     * @param ActiveRecord|null $record
     */
    public function __construct(ModelComponent $model, $config = [], ActiveRecord $record = null)
    {
        parent::__construct($config);
        $this->_model = $model;
        $this->_columns = $this->getModelAttributes($model->columns);
        $this->_record = $record;

        foreach ($this->_columns as $columnName => $column) {
            $this->$columnName = null;
            if ($column instanceof HasManyColumn) {
                $this->$columnName = [];
            }
        }
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        $params = [];

        foreach ($this->_columns as $columnName => $column) {
            $params[] = $columnName;
        }

        return [
            self::SCENARIO_CREATE => $params,
            self::SCENARIO_UPDATE => $params,
        ];
    }

    /**
     * @param string $attribute
     *
     * @return void
     */
    public function validateModelAttribute(string $attribute): void
    {
        $result = $this->_columns[$attribute]->validate($this->$attribute);

        if ($result !== null) {
            $this->addError($attribute, $result);
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $rules = [];

        foreach ($this->_columns as $columnName => $column) {
            $rules[] = [$columnName, 'validateModelAttribute'];
            foreach ($column->getValidators() as $validator) {
                $rule = [$columnName];

                if (is_array($validator)) {
                    if ($this->getScenario() === self::SCENARIO_UPDATE && $validator[0] === 'unique' && !in_array(
                            'filter',
                            $validator
                        )) {
                        $validator['filter'] = function (Query $query) {
                            return $query->andWhere(
                                [
                                    '<>',
                                    get_class($this->_record)::primaryKey()[0],
                                    $this->_record->getPrimaryKey(),
                                ]
                            );
                        };
                    }
                    $rule = array_merge($rule, $validator);
                } else {
                    if ($this->getScenario() === self::SCENARIO_UPDATE && $validator === 'unique') {
                        $validator = ['unique'];
                        $validator['filter'] = function (Query $query) {
                            return $query->andWhere(
                                [
                                    '<>',
                                    get_class($this->_record)::primaryKey()[0],
                                    $this->_record->getPrimaryKey(),
                                ]
                            );
                        };

                        $rule = array_merge($rule, $validator);
                    } else {
                        $rule[] = $validator;
                    }
                }

                $rules[] = $rule;
            }
        }

        return $rules;
    }

    /**
     * @return ActiveRecord|null
     */
    public function createRecord(): ?ActiveRecord
    {
        foreach ($this->_columns as $columnName => $column) {
            if ($column instanceof FileColumn) {
                $this->$columnName = UploadedFile::getInstance($this, $columnName);
            }
        }

        if ($this->validate()) {
            $class = $this->_model->class;

            return $this->createActiveRecord(new $class());
        }

        return null;
    }

    /**
     * @return ColumnInterface[]
     */
    public function getModelColumns(): array
    {
        return $this->_columns;
    }

    /**
     * @param string $attribute
     *
     * @return string
     */
    public function getAttributeLabel($attribute): string
    {
        return parent::getAttributeLabel(str_replace('model_', '', $attribute));
    }

    /**
     * @param ActiveRecord $record
     *
     * @param int          $mode
     *
     * @return ActiveRecord
     */
    private function createActiveRecord(ActiveRecord $record, int $mode = 0): ActiveRecord
    {
        foreach ($this->_columns as $columnName => $column) {
            $fixedName = str_replace(self::ATTRIBUTE_PREFIX, '', $columnName);
            $parts = explode('__', $fixedName);

            if (count($parts) > 1) {
                if ($mode === 0) {
                    if ($this->$columnName === '') {
                        $columnNameParts = explode('__', $columnName);
                        array_pop($columnNameParts);
                        $nameRelation = implode('__', $columnNameParts).'__';

                        $this->_countInRelations[$nameRelation]--;

                        continue;
                    }

                    $this->createActiveRecordByParts(
                        $this->_model->columns[$parts[0]],
                        $parts,
                        '_model',
                        $this->$columnName,
                        $columnName
                    );
                } else {
                    $this->createActiveRecordByParts(
                        $this->_model->columns[$parts[0]],
                        $parts,
                        '_model',
                        $this->$columnName,
                        $columnName,
                        $record
                    );
                }
            } else {
                if ($this->_model->columns[$fixedName] instanceof HasManyColumn) {
                    $relationName = NameHelper::getRelationColumnName($fixedName);
                    if ($mode === 0) {
                        $records = $this->_model->columns[$fixedName]->getTargetClass()::find()->all();
                        $listRecords = [];

                        foreach ($records as $rec) {
                            $listRecords[$rec->getPrimaryKey()] = $rec;
                        }

                        if (!is_array($this->$columnName)) {
                            $this->$columnName = [];
                        }
                        $this->_relations['_model'][$columnName] = [];
                        foreach ($this->$columnName as $valueId) {
                            $this->_relations['_model'][$columnName][] = $listRecords[$valueId];
                        }
                    } else {
                        /** @var ActiveRecord[] $records */
                        $records = $record->$relationName;

                        foreach ($records as $rec) {
                            array_push($this->$columnName, $rec->getPrimaryKey());
                        }
                    }

                    continue;
                }
                if ($this->_model->columns[$fixedName] instanceof HasOneColumn && $this->_model->columns[$fixedName]->getMethod(
                    ) === HasOneColumn::METHOD_SELECT) {
                    if ($mode === 0) {
                        $this->_relations['_model'][$columnName.'__'] = $this->_model->columns[$fixedName]->getTargetClass(
                        )::findOne(['id' => $this->$columnName])
                        ;
                    } else {
                        $fixedName = NameHelper::getRelationColumnName($fixedName);
                        $this->$columnName = $record->$fixedName;
                    }

                    continue;
                }
                if ($mode === 0) {
                    $column->afterChange($record, $fixedName, $this->$columnName);
                    if (($setter = $this->_model->columns[$fixedName]->getSetter()) !== null) {
                        $record->$setter($this->$columnName);
                    } else {
                        $record->$fixedName = $this->$columnName;
                    }
                } else {
                    $this->$columnName = $record->$fixedName;
                }
            }
        }

        $record->save();

        if ($mode === 0) {
            $this->createRelationRecord($record, '_model');
        }

        return $record;
    }

    /**
     * @param ActiveRecord $parentRecord
     * @param string       $parentName
     */
    private function createRelationRecord(ActiveRecord $parentRecord, string $parentName): void
    {
        if (isset($this->_relations[$parentName])) {
            foreach ($this->_relations[$parentName] as $relationName => $relationRecord) {
                if (!is_array(
                        $relationRecord
                    ) && isset($this->_countInRelations[$relationName]) && $this->_countInRelations[$relationName] === 0) {
                    continue;
                }

                $relationNameParts = explode('__', $relationName);
                $del = 2;
                if (is_array($relationRecord)) {
                    $del--;
                }
                $relationName = NameHelper::getRelationColumnName(
                    str_replace(self::ATTRIBUTE_PREFIX, '', $relationNameParts[count($relationNameParts) - $del])
                );
                if (is_array($relationRecord)) {
                    /** @var ActiveRecord[] $oldRecords */
                    $oldRecords = $parentRecord->$relationName;
                    $oldRecordsKeys = [];
                    $newRecordsKeys = [];
                    foreach ($oldRecords as $oldRecord) {
                        $oldRecordsKeys[$oldRecord->getPrimaryKey()] = $oldRecord;
                        //$parentRecord->link($relationName, $record);
                    }

                    /** @var ActiveRecord $record */
                    foreach ($relationRecord as $record) {
                        $newRecordsKeys[$record->getPrimaryKey()] = $record;
                    }

                    foreach ($oldRecordsKeys as $oldRecord) {
                        if (!isset($newRecordsKeys[$oldRecord->getPrimaryKey()])) {
                            $parentRecord->unlink($relationName, $oldRecord, true);
                        }
                    }

                    foreach ($newRecordsKeys as $newRecord) {
                        if (!isset($oldRecordsKeys[$newRecord->getPrimaryKey()])) {
                            $parentRecord->link($relationName, $newRecord);
                        }
                    }
                } else {
                    /** @var ActiveRecord $relationRecord */
                    if ($parentRecord->$relationName === null || !$relationRecord->isNewRecord) {
                        $parentRecord->link($relationName, $relationRecord);
                    }
                    $relationRecord->save();
                    $this->createRelationRecord($relationRecord, $relationName);
                }
            }
        }
    }

    /**
     * @param RelationColumnInterface $column
     * @param array                   $parts
     * @param string                  $parent
     * @param mixed                   $value
     * @param string                  $modelColumnName
     * @param ActiveRecord|null       $parentRecord
     */
    private function createActiveRecordByParts(
        RelationColumnInterface $column,
        array $parts,
        string $parent,
        $value,
        string $modelColumnName,
        ?ActiveRecord $parentRecord = null
    ): void {
        $columnNameParts = explode('__', $modelColumnName);
        array_pop($columnNameParts);
        $nameRelation = implode('__', $columnNameParts).'__';
        $nameColumn = NameHelper::getRelationColumnName(array_shift($parts));

        if (!isset($this->_relations[$parent][$nameRelation])) {
            if ($parentRecord !== null) {
                $this->_relations[$parent][$nameRelation] = $parentRecord->$nameColumn;
                if ($this->_relations[$parent][$nameRelation] === null) {
                    return;
                }
            } else {
                $class = $column->getTargetClass();
                /** @var ActiveRecord $record */
                $record = new $class();
                $this->_relations[$parent][$nameRelation] = $record;
            }
        }

        if (count($parts) > 0) {
            $nextColumnName = $parts[0];
            $nextColumn = $column->getColumns()[$nextColumnName];

            if ($nextColumn instanceof HasOneColumn) {
                if ($nextColumn->getMethod() === HasOneColumn::METHOD_CREATE) {
                    if ($parentRecord !== null) {
                        $this->createActiveRecordByParts(
                            $nextColumn,
                            $parts,
                            $nameColumn,
                            $value,
                            $modelColumnName,
                            $this->_relations[$parent][$nameRelation]
                        );
                    } else {
                        $this->createActiveRecordByParts($nextColumn, $parts, $nameColumn, $value, $modelColumnName);
                    }
                } elseif ($nextColumn->getMethod() === HasOneColumn::METHOD_SELECT) {
                    if ($parentRecord !== null) {
                        $this->$modelColumnName = $this->_relations[$parent][$nameRelation];
                    } else {
                        $this->_relations[$parent][$nameRelation] = $nextColumn->getTargetClass()::findOne(
                            ['id' => $value]
                        )
                        ; // todo: change id to primary key
                    }
                }
            } elseif ($nextColumn instanceof HasManyColumn) {
                if ($parentRecord === null) {
                    $records = $nextColumn->getTargetClass()::find()->all();
                    $listRecords = [];

                    foreach ($records as $rec) {
                        $listRecords[$rec->getPrimaryKey()] = $rec;
                    }

                    if (!is_array($value)) {
                        $value = [];
                    }

                    $this->_relations[$parent][$nameRelation] = [];
                    foreach ($value as $valueId) {
                        $this->_relations[$parent][$nameRelation][] = $listRecords[$valueId];
                    }
                } else {
                    $relationName = NameHelper::getRelationColumnName($nextColumnName);
                    /** @var ActiveRecord[] $records */
                    $records = $parentRecord->$relationName;

                    foreach ($records as $rec) {
                        array_push($this->$modelColumnName, $rec->getPrimaryKey());
                    }
                }
            } else {
                if ($parentRecord === null) {
                    if (($setter = $nextColumn->getSetter()) !== null) {
                        $this->_relations[$parent][$nameRelation]->$setter($value);
                    } else {
                        $this->_relations[$parent][$nameRelation]->$nextColumnName = $value;
                    }
                } else {
                    $this->$modelColumnName = $this->_relations[$parent][$nameRelation]->$nextColumnName;
                }
            }
        }
    }

    /**
     * @param ColumnInterface[] $columns
     *
     * @param string            $prefix
     *
     * @return array
     */
    private function getModelAttributes(array $columns, $prefix = ''): array
    {
        $result = [];

        foreach ($columns as $columnName => $column) {
            if ($column instanceof HasOneColumn && $column->getMethod() === HasOneColumn::METHOD_CREATE) {
                $result = array_merge(
                    $result,
                    $this->getModelAttributes($column->getColumns(), $prefix.$columnName.'__')
                );

                continue;
            }

            $result[self::ATTRIBUTE_PREFIX.$prefix.$columnName] = $column;

            if ($prefix !== '') {
                if (!isset($this->_countInRelations[self::ATTRIBUTE_PREFIX.$prefix])) {
                    $this->_countInRelations[self::ATTRIBUTE_PREFIX.$prefix] = 0;
                }
                $this->_countInRelations[self::ATTRIBUTE_PREFIX.$prefix]++;
            }
        }

        return $result;
    }

    /**
     * @param ActiveRecord $record
     */
    public function fill(ActiveRecord $record): void
    {
        $this->createActiveRecord($record, 1);
    }

    /**
     * @param ActiveRecord $fillModel
     *
     * @return bool
     */
    public function updateRecord(ActiveRecord $fillModel): bool
    {
        foreach ($this->_columns as $columnName => $column) {
            if ($column instanceof FileColumn) {
                $this->$columnName = UploadedFile::getInstance($this, $columnName);

                if ($this->$columnName === null) {
                    $fixedName = str_replace(self::ATTRIBUTE_PREFIX, '', $columnName);
                    $this->$columnName = $fillModel->$fixedName;
                }
            }
        }

        if ($this->validate()) {
            $this->createActiveRecord($fillModel);
            $fillModel->save();

            return true;
        }

        return false;
    }
}
