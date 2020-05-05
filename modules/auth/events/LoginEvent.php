<?php

namespace auth\events;

use Throwable;
use Yii;
use yii\base\ActionEvent;
use yii\web\UserEvent;

class LoginEvent
{
    /**
     * @param ActionEvent $event
     *
     * @throws Throwable
     */
    public static function handler(ActionEvent $event): void
    {
        if (!Yii::$app->user->isGuest &&
            Yii::$app->user->getIdentity()->confirm_phone !== null &&
            $event->action->id !== 'confirm-phone' &&
            $event->action->id !== 'enter-phone') {
            Yii::$app->controller->redirect('/phone/set');
        }
    }

    /**
     * @param UserEvent $event
     */
    public static function handlerAfterLogin(UserEvent $event): void
    {
        if ($event->identity->confirm_phone !== null) {
            Yii::$app->controller->redirect('/confirm/phone');
        }
    }
}
