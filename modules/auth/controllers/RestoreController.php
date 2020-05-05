<?php

declare(strict_types=1);

namespace auth\controllers;

use auth\models\forms\RegisterForm;
use auth\models\forms\RestoreForm;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class RestoreController
 */
class RestoreController extends Controller
{
    /**
     * @return string|Response
     */
    public function actionEnterEmail()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $restoreForm = new RestoreForm(['scenario' => RestoreForm::SCENARIO_ENTER_EMAIL]);

        if ($restoreForm->load(Yii::$app->request->post()) && $restoreForm->sendRestoreCode()) {
            return $this->redirect('/password/change');
        }

        return $this->render(
            'enter_email',
            [
                'restoreForm' => $restoreForm,
            ]
        );
    }

    /**
     * @return string|Response
     */
    public function actionNewPassword()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $restoreForm = new RestoreForm(['scenario' => RestoreForm::SCENARIO_CONFIRM_EMAIL]);

        $getEmail = Yii::$app->request->get('email');
        $getCode = Yii::$app->request->get('code');
        $isNew = $getCode !== null || $getEmail !== null;

        $restoreForm->setAttributes(
            [
                'email'            => $getEmail,
                'confirmEmailCode' => $getCode,
            ]
        );

        if ($isNew && $restoreForm->validate()) {
            $restoreForm->setScenario(RestoreForm::SCENARIO_NEW_PASSWORD);

            return $this->render(
                'new_password',
                [
                    'restoreForm' => $restoreForm,
                ]
            );
        }

        $restoreForm->setScenario(RestoreForm::SCENARIO_NEW_PASSWORD);
        if (!$isNew && $restoreForm->load(Yii::$app->request->post())) {
            if ($restoreForm->setNewPassword()) {
                return $this->render('success');
            }

            return $this->render(
                'new_password',
                [
                    'restoreForm' => $restoreForm,
                ]
            );
        }

        return $this->render('confirm_email');
    }
}
