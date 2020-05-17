<?php /** @noinspection ALL */

declare(strict_types=1);

namespace auth\controllers;

use auth\models\forms\ProfileForm;
use auth\models\forms\RegisterForm;
use auth\models\forms\RestoreForm;
use Throwable;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class ProfileController
 */
class ProfileController extends Controller
{
    /**
     * @return string|Response
     *
     * @throws Throwable
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        return $this->render(
            'index',
            [
                'user' => Yii::$app->user->getIdentity(),
            ]
        );
    }

    /**
     * @return string
     *
     * @throws Throwable
     */
    public function actionChangeEmail(): string
    {
        $profileForm = new ProfileForm(['scenario' => ProfileForm::SCENARIO_CHANGE_EMAIL]);

        if ($profileForm->load(Yii::$app->request->post()) &&
            $profileForm->changeEmail(Yii::$app->user->getIdentity())) {
            return $this->render('confirm_email');
        }

        return $this->render(
            'change_email.php',
            [
                'profileForm' => $profileForm,
            ]
        );
    }

    /**
     * @return Response
     */
    public function actionConfirmEmail(): Response
    {
        $getEmail = Yii::$app->request->get('email');
        $getCode = Yii::$app->request->get('code');

        $profileForm = new ProfileForm(['scenario' => ProfileForm::SCENARIO_CONFIRM_EMAIL]);
        $profileForm->setAttributes(
            [
                'email'            => $getEmail,
                'confirmEmailCode' => $getCode,
            ]
        );

        $profileForm->confirmEmail(Yii::$app->user->getIdentity());

        return $this->goHome();
    }

    /**
     * @return string|Response
     */
    public function actionChangePassword()
    {
        $profileForm = new ProfileForm(['scenario' => ProfileForm::SCENARIO_CHANGE_PASSWORD]);

        if ($profileForm->load(Yii::$app->request->post()) &&
            $profileForm->changePassword(Yii::$app->user->getIdentity())) {
            return $this->goHome();
        }

        return $this->render(
            'change_password',
            [
                'profileForm' => $profileForm,
            ]
        );
    }

    /**
     * @return string|Response
     */
    public function actionChangePhone()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $profileForm = new ProfileForm(['scenario' => ProfileForm::SCENARIO_ENTER_PHONE]);

        if ($profileForm->load(Yii::$app->request->post())
            && $profileForm->changePhone(Yii::$app->user->getIdentity())) {
            return $this->redirect('/profile');
        }

        return $this->render(
            'phone_form',
            [
                'profileForm' => $profileForm,
            ]
        );
    }
}
