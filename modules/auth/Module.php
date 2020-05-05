<?php

declare(strict_types=1);

namespace auth;

use auth\events\LoginEvent;
use Yii;
use yii\base\BootstrapInterface;
use yii\web\Controller;

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
        $this->setAliases(['@auth' => __DIR__]);

        $moduleConfig = require __DIR__.'/config/config.php';

        Yii::$app->params['sms_repeat_send'] = $moduleConfig['sms_repeat_send'];
        Yii::$app->params['confirm_phone'] = $moduleConfig['confirm_phone'];

        $this->on(Controller::EVENT_AFTER_ACTION, [LoginEvent::class, 'handler']);

        $app->getUrlManager()->enablePrettyUrl = true;
        $app->getUrlManager()->showScriptName = false;
        $app->getUrlManager()->addRules($moduleConfig['urls'], false);
    }
}
