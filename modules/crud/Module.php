<?php

declare(strict_types=1);

namespace crud;

use Yii;
use yii\base\BootstrapInterface;

/**
 * Class Module
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();

        $moduleConfig = require __DIR__.'/config/config.php';

        Yii::$app->getUrlManager()->enablePrettyUrl = true;
        Yii::$app->getUrlManager()->showScriptName = false;
        Yii::$app->getUrlManager()->addRules($moduleConfig['urls'], false);
    }

    /**
     * @inheritDoc
     */
    public function bootstrap($app): void
    {

    }
}
