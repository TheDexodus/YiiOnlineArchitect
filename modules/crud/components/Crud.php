<?php

declare(strict_types=1);

namespace crud\components;

use Yii;
use yii\base\Component;

/**
 * Class Crud
 */
class Crud extends Component
{
    /** @var Model[] */
    private $_models = [];

    /**
     * @param string $configFile
     */
    public function setConfigFile(string $configFile): void
    {
        $config = require $configFile;
        $models = $config['use_models'] ?? [];

        foreach ($models as $modelName => $modelConfig) {
            $this->_models[$modelName] = new Model($modelName, $modelConfig);
        }
    }

    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();

        Yii::$app->getModule('crud');
    }

    /**
     * @return Model[]
     */
    public function getModelList(): array
    {
        return $this->_models;
    }

    /**
     * @param string $modelName
     *
     * @return Model|null
     */
    public function getModel(string $modelName): ?Model
    {
        return $this->_models[$modelName] ?? null;
    }

    /**
     * @param string $action
     *
     * @return Model[]
     */
    public function getModelsForAction(string $action): array
    {
        return array_filter(
            $this->_models,
            function ($model) use ($action) {
                /** @var Model $model */
                return $model->canAction($action);
            }
        );
    }
}
