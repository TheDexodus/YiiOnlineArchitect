<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int        $id
 * @property string     $display_name
 * @property string     $usage
 * @property string     $measurements
 * @property string     $typical_consumption
 * @property Material[] $materials
 */
class MaterialType extends ActiveRecord implements ImportableInterface
{
    public const USAGE_WALLS  = 'walls';
    public const USAGE_FLOORS = 'floors';
    public const USAGE_CELLS  = 'cells';

    public const MEASUREMENT_LITRES         = 'litres';
    public const MEASUREMENT_RUNNING_METRES = 'running metres';
    public const MEASUREMENT_KILOGRAMMES    = 'kilogrammes';

    public const TYPICAL_CONSUMPTION_UNITS_PER_M2 = 'units per m^2';

    /**
     * @inheritDoc
     */
    public static function tableName(): string
    {
        return 'material_types';
    }

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            [['display_name', 'usage', 'measurements', 'typical_consumption'], 'required'],
            ['usage', 'in', 'range' => [self::USAGE_CELLS, self::USAGE_WALLS, self::USAGE_FLOORS]],
            [
                'measurements',
                'in',
                'range' => [self::MEASUREMENT_KILOGRAMMES, self::MEASUREMENT_RUNNING_METRES, self::MEASUREMENT_LITRES],
            ],
            ['typical_consumption', 'in', 'range' => [self::TYPICAL_CONSUMPTION_UNITS_PER_M2]],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getMaterials(): ActiveQuery
    {
        return $this->hasMany(Material::class, ['type_id' => 'id']);
    }

    /**
     * @inheritDoc
     */
    public function getRelationMaps(): array
    {
        return [];
    }
}
