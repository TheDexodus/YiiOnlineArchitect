<?php

declare(strict_types=1);

namespace wizard;

use yii\base\BootstrapInterface;

class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();

        //Yii::configure($this, require __DIR__.'/config/config.php');
    }

    /**
     * @inheritDoc
     */
    public function bootstrap($app): void
    {
        $this->setAliases(['@wizard' => __DIR__]);

        $moduleConfig = require __DIR__.'/config/config.php';

        $app->getUrlManager()->enablePrettyUrl = true;
        $app->getUrlManager()->showScriptName = false;
        $app->getUrlManager()->addRules($moduleConfig['urls'], false);
    }
}
