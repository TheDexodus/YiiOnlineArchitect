<?php

class Importer extends \yii\base\Component
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