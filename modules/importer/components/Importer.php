<?php

use yii\base\Component;

class Importer extends Component
{
    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();

        Yii::$app->getModule('crud');
    }
}
