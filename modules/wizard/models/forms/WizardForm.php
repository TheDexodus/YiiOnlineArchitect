<?php

declare(strict_types=1);

namespace wizard\models\forms;

use app\models\Material;
use app\models\MaterialType;
use yii\base\Model;

/**
 * Class WizardForm
 */
class WizardForm extends Model
{
    public const SCENARIO_SET_ROOM_SETTINGS = 'set_room_settings';
    public const SCENARIO_SET_OPENINGS      = 'set_openings';
    public const SCENARIO_SELECT_MATERIAL   = 'select_material';
    public const SCENARIO_CONFIRM           = 'confirm';
    public const SCENARIO_GENERATE_BILL     = 'generate_bill';

    /** @var float */
    public $floor_width;

    /** @var float */
    public $floor_height;

    /** @var float */
    public $wall_height;

    /** @var array */
    public $openings = [];

    /** @var array */
    public $usage_material = [];

    /** @var array */
    public $materials = [];

    /** @var array */
    public $goodMaterials = [];

    /**
     * @inheritDoc
     */
    public function scenarios(): array
    {
        return [
            self::SCENARIO_SET_ROOM_SETTINGS => [
                'floor_width',
                'floor_height',
                'wall_height',
            ],
            self::SCENARIO_SET_OPENINGS      => [
                'openings',
            ],
            self::SCENARIO_SELECT_MATERIAL   => [
                'usage_material',
                'materials',
            ],
            self::SCENARIO_CONFIRM           => [],
            self::SCENARIO_GENERATE_BILL     => [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            [['floor_width', 'floor_height', 'wall_height'], 'required', 'on' => self::SCENARIO_SET_ROOM_SETTINGS],
            ['openings', 'required', 'on' => self::SCENARIO_SET_OPENINGS],
            [['materials', 'usage_material'], 'required', 'on' => self::SCENARIO_SELECT_MATERIAL],
            [['floor_width', 'floor_height', 'wall_height'], 'number', 'min' => 0.01],
            ['openings', 'validateOpenings'],
            ['materials', 'validateMaterials'],
            ['usage_material', 'validateUsageMaterials'],
        ];
    }

    /**
     * @param string|null $type
     *
     * @return float
     */
    public function getSquare(string $type = null): float
    {
        $floors = 0;
        $cells = 0;
        $walls = 0;

        if (isset($this->goodMaterials['floors'])) {
            $floors = (float)$this->floor_width * (float)$this->floor_height;
        }

        if (isset($this->goodMaterials['cells'])) {
            $cells = (float)$this->floor_width * (float)$this->floor_height;
        }

        if (isset($this->goodMaterials['walls'])) {
            $openingArea = 0;

            foreach ($this->openings as $item) {
                $openingArea += $item['width'] * $item['height'];
            }

            $walls = (float)$this->floor_width * (float)$this->wall_height * 2 + (float)$this->floor_height * (float)$this->wall_height * 2 - $openingArea;
        }

        switch ($type) {
            case 'floors':
                return $floors;
            case 'cells':
                return $cells;
            case 'walls':
                return $walls;
        }

        return $floors + $cells + $walls;
    }

    /**
     * @param string $type
     *
     * @return int
     */
    public function getCount(string $type = null): int
    {
        $floors = 0;
        $cells = 0;
        $walls = 0;

        if (isset($this->goodMaterials['floors'])) {
            $floors = (int)ceil($this->getSquare('floors') * $this->goodMaterials['floors']->multiplier);
        }

        if (isset($this->goodMaterials['cells'])) {
            $cells = (int)ceil($this->getSquare('cells') * $this->goodMaterials['cells']->multiplier);
        }

        if (isset($this->goodMaterials['walls'])) {
            $walls = (int)ceil($this->getSquare('walls') * $this->goodMaterials['walls']->multiplier);
        }

        switch ($type) {
            case 'floors':
                return $floors;
            case 'cells':
                return $cells;
            case 'walls':
                return $walls;
        }

        return $floors + $cells + $walls;
    }

    /**
     * @param string $type
     *
     * @return float
     */
    public function getPrice(string $type = null): float
    {
        $floors = 0;
        $cells = 0;
        $walls = 0;

        if (isset($this->goodMaterials['floors'])) {
            $floors = $this->getCount('floors') * $this->goodMaterials['floors']->price;
        }

        if (isset($this->goodMaterials['cells'])) {
            $cells = $this->getCount('cells') * $this->goodMaterials['cells']->price;
        }

        if (isset($this->goodMaterials['walls'])) {
            $walls = $this->getCount('walls') * $this->goodMaterials['walls']->price;
        }

        switch ($type) {
            case 'floors':
                return $floors;
            case 'cells':
                return $cells;
            case 'walls':
                return $walls;
        }

        return $floors + $cells + $walls;
    }

    /**
     * @param string $attribute
     */
    public function validateMaterials(string $attribute): void
    {
        foreach ($this->materials as $key => &$material) {
            if (!in_array($key, $this->usage_material)) {
                unset($material);

                return;
            }

            $isReal = false;

            /** @var Material $material */
            foreach ($this->goodMaterials[$key] as $goodMaterial) {
                if ($goodMaterial->vendor_code == $material) {
                    $isReal = true;

                    break;
                }
            }

            if (!$isReal) {
                $this->addError($attribute, 'Invalid format');

                return;
            }
        }
    }

    /**
     * @param string $attribute
     */
    public function validateUsageMaterials(string $attribute): void
    {
        if (!is_array($this->usage_material)) {
            $this->addError($attribute, 'Incorrect data');

            return;
        }

        foreach ($this->usage_material as $materialType) {
            if ($materialType !== MaterialType::USAGE_WALLS &&
                $materialType !== MaterialType::USAGE_FLOORS &&
                $materialType !== MaterialType::USAGE_CELLS
            ) {
                $this->addError($attribute, 'Incorrect data');

                return;
            } else {
                if (!isset($this->materials[$materialType])) {
                    $this->addError($attribute, 'You must select a material for your type');

                    return;
                }
            }
        }
    }

    /**
     * @param string $attribute
     */
    public function validateOpenings(string $attribute): void
    {
        if (!is_array($this->openings)) {
            $this->addError($attribute, 'Invalid format');

            return;
        }

        if (count($this->openings) === 0) {
            return;
        }

        $wallArea = ($this->floor_height * $this->wall_height) * 2 + ($this->wall_height * $this->floor_width) * 2;
        $openingArea = 0;

        foreach ($this->openings as &$opening) {
            if (!isset($opening['width']) ||
                !isset($opening['height']) ||
                !is_numeric($opening['width']) ||
                !is_numeric($opening['height']) ||
                $opening['width'] < 0.01 ||
                $opening['height'] < 0.01
            ) {
                $this->addError($attribute, 'Invalid format');

                return;
            } else {
                $opening['width'] = floor($opening['width'] * 100) / 100;
                $opening['height'] = floor($opening['height'] * 100) / 100;

                $openingArea += $opening['width'] * $opening['height'];
            }
        }

        if ($wallArea <= $openingArea) {
            $this->addError($attribute, 'Invalid values');
        }
    }
}
