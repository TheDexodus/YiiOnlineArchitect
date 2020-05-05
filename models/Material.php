<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int          $id
 * @property int          $vendor_code
 * @property string       $display_name
 * @property MaterialType $type
 * @property string       $color
 * @property string       $use_pattern
 * @property string       $picture
 * @property int          $price
 * @property int          $multiplier
 * @property mixed        $details
 */
class Material extends ActiveRecord implements ImportableInterface
{
    /**
     * @inheritDoc
     */
    public static function tableName(): string
    {
        return 'materials';
    }

    /**
     * @return ActiveQuery
     */
    public function getType(): ActiveQuery
    {
        return $this->hasOne(MaterialType::class, ['id' => 'type_id']);
    }

    /**
     * @inheritDoc
     */
    public function getRelationMaps(): array
    {
        return [
            'type_id' => [
                'class'         => MaterialType::class,
                'attribute'     => 'type',
                'target_column' => 'id',
            ],
        ];
    }
}
