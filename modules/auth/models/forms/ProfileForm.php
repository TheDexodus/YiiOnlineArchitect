<?php

declare(strict_types=1);

namespace auth\models\forms;

use auth\components\helpers\SendHelper;
use auth\models\User;
use borales\extensions\phoneInput\PhoneInputValidator;
use Yii;
use yii\base\Model;

/**
 * Class ProfileForm
 */
class ProfileForm extends Model
{
    const SCENARIO_CHANGE_EMAIL    = 'change-email';
    const SCENARIO_CONFIRM_EMAIL   = 'confirm-email';
    const SCENARIO_CHANGE_PASSWORD = 'change-password';
    const SCENARIO_ENTER_PHONE     = 'change-phone';

    /** @var string $email */
    public $email;

    /** @var string $confirmEmailCode */
    public $confirmEmailCode;

    /** @var string $oldPassword */
    public $oldPassword;

    /** @var string $newPassword */
    public $newPassword;

    /** @var string $phone */
    public $phone;

    /** @var string $confirmNewPassword */
    public $confirmNewPassword;

    /**
     * @return array
     */
    public function scenarios(): array
    {
        return [
            self::SCENARIO_CHANGE_EMAIL    => [
                'email',
            ],
            self::SCENARIO_CONFIRM_EMAIL   => [
                'email',
                'confirmEmailCode',
            ],
            self::SCENARIO_CHANGE_PASSWORD => [
                'oldPassword',
                'newPassword',
                'confirmNewPassword',
            ],
            self::SCENARIO_ENTER_PHONE  => [
                'phone',
            ],
        ];
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function changePhone(User $user): bool
    {
        if ($this->validate()) {
            if (!Yii::$app->params['confirm_phone']) {
                $user->phone = $this->phone;
                $user->confirm_phone = null;
                $user->save();

                return true;
            } else {
                $this->addError('phone', 'Try later');
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['email', 'required', 'on' => self::SCENARIO_CHANGE_EMAIL],
            [['email', 'confirmEmailCode'], 'required', 'on' => self::SCENARIO_CHANGE_EMAIL],
            ['email', 'unique', 'targetClass' => User::class],
            ['email', 'email'],
            ['phone', 'required', 'on' => self::SCENARIO_ENTER_PHONE],
            ['phone', PhoneInputValidator::class],
            ['newPassword', 'validatePassword'],
        ];
    }

    /**
     * @param string $attribute
     */
    public function validatePassword(string $attribute)
    {
        if (!preg_match_all('/[a-z0-9!#$%&\'()*+,\\-.\\/:;<=>?@[\\\\\]^_`{|}~"]/i', $this->newPassword)) {
            $this->addError($attribute, 'Incorrect password');
        } elseif ($this->newPassword !== $this->confirmNewPassword) {
            $this->addError('confirmNewPassword', 'Passwords do not match');
        }
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function changeEmail(User $user): bool
    {
        if ($this->validate()) {
            $user->change_email = $this->email;

            SendHelper::sendConfirmEmailMessage(
                $this->email,
                $user->generateConfirmEmailCode(),
                'confirm_change_email'
            );

            $user->save();

            return true;
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function confirmEmail(User $user): bool
    {
        if ($user->change_email === $this->email && $user->validateConfirmEmail($this->confirmEmailCode)) {
            $user->change_email = null;
            $user->email = $this->email;
            $user->save();

            return true;
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function changePassword(User $user): bool
    {
        if ($this->validate()) {
            if (!$user->validatePassword($this->oldPassword)) {
                $this->addError('oldPassword', 'Password entered is incorrect');
            } else {
                $user->setPassword($this->newPassword);
                $user->save();

                return true;
            }
        }

        return false;
    }
}
