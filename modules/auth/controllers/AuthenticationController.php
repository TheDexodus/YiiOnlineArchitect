<?php

declare(strict_types=1);

namespace auth\controllers;

use auth\models\forms\LoginForm;
use auth\models\forms\RegisterForm;
use Throwable;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class AuthenticationController
 */
class AuthenticationController extends Controller
{
    /**
     * @return Response|string
     */
    public function actionLogin()
    {

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $loginForm = new LoginForm();

        if ($loginForm->load(Yii::$app->request->post()) && $loginForm->login()) {
            return $this->goBack();
        }

        return $this->render(
            'login',
            [
                'loginForm' => $loginForm,
            ]
        );
    }

    /**
     * @return Response|string
     */
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $registerForm = new RegisterForm(['scenario' => RegisterForm::SCENARIO_REGISTER]);

        if ($registerForm->load(Yii::$app->request->post()) && $registerForm->register()) {
            return $this->redirect('/confirm/email');
        }

        return $this->render(
            'register',
            [
                'registerForm' => $registerForm,
            ]
        );
    }

    /**
     * @return string|Response
     */
    public function actionConfirmEmail()
    {
        $registerForm = new RegisterForm(['scenario' => RegisterForm::SCENARIO_CONFIRM_EMAIL]);

        if ($registerForm->confirmEmail(
            (string) Yii::$app->request->get('email'),
            (string) Yii::$app->request->get('code')
        )) {
            return $this->redirect('/confirm/phone');
        }

        return $this->render('confirm_email');
    }

    /**
     * @return string|Response
     *
     * @throws Throwable
     */
    public function actionEnterPhone()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $registerForm = new RegisterForm(['scenario' => RegisterForm::SCENARIO_ENTER_PHONE]);

        if ($registerForm->load(Yii::$app->request->post())
            && $registerForm->sendPhoneCode(Yii::$app->user->getIdentity())) {
            return $this->redirect('/confirm/phone');
        }

        return $this->render(
            'phone_form',
            [
                'registerForm' => $registerForm,
            ]
        );
    }

    /**
     * @return string|Response
     *
     * @throws Throwable
     */
    public function actionConfirmPhone()
    {
        if (Yii::$app->user->isGuest || Yii::$app->user->identity->phone !== null) {
            return $this->goHome();
        }

        $registerForm = new RegisterForm(['scenario' => RegisterForm::SCENARIO_CONFIRM_PHONE]);

        if ($registerForm->load(Yii::$app->request->post())
            && $registerForm->confirmPhone(Yii::$app->user->getIdentity())) {
            return $this->goHome();
        }

        return $this->render(
            'confirm_phone',
            [
                'renewTime'    => Yii::$app->user->getIdentity()->smsRequest->getRenewTime(),
                'registerForm' => $registerForm,
            ]
        );
    }

    /**
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
