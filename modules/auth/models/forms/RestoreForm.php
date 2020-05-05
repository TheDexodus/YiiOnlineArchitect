<?php

declare(strict_types=1);

namespace auth\models\forms;

use auth\components\helpers\SendHelper;
use auth\models\User;
use borales\extensions\phoneInput\PhoneInputValidator;
use yii\base\Model;

/**
 * Class RestoreForm
 */
class RestoreForm extends Model
{
    const SCENARIO_ENTER_EMAIL   = 'enter-email';
    const SCENARIO_CONFIRM_EMAIL = 'confirm-email';
    const SCENARIO_NEW_PASSWORD  = 'new-password';

    /** @var string $email */
    public $email;

    /** @var string $confirmEmailCode */
    public $confirmEmailCode;

    /** @var string $newPassword */
    public $newPassword;

    /** @var string $confirmNewPassword */
    public $confirmNewPassword;

    /** @var User|false $_user */
    private $_user = false;

    /**
     * @return array
     */
    public function scenarios(): array
    {
        return [
            self::SCENARIO_ENTER_EMAIL   => [
                'email',
            ],
            self::SCENARIO_CONFIRM_EMAIL => [
                'email',
                'confirmEmailCode',
            ],
            self::SCENARIO_NEW_PASSWORD  => [
                'email',
                'confirmEmailCode',
                'newPassword',
                'confirmNewPassword',
            ],
        ];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['email', 'required', 'on' => self::SCENARIO_ENTER_EMAIL],
            [['email, confirmEmailCode'], 'required', 'on' => self::SCENARIO_CONFIRM_EMAIL],
            [
                ['email, confirmEmailCode', 'newPassword', 'confirmNewPassword'],
                'required',
                'on' => self::SCENARIO_NEW_PASSWORD,
            ],
            ['newPassword', 'validatePassword'],
            ['email', 'email'],
        ];
    }

    /**
     * @param string $attribute
     */
    public function validatePassword(string $attribute): void
    {
        if (!preg_match_all('/[a-z0-9!#$%&\'()*+,\\-.\\/:;<=>?@[\\\\\]^_`{|}~"]/i', $this->newPassword)) {
            $this->addError($attribute, 'Incorrect password');
        } elseif ($this->newPassword !== $this->confirmNewPassword) {
            $this->addError($attribute, 'Passwords do not match');
        }
    }

    /**
     * @return bool
     */
    public function sendRestoreCode(): bool
    {
        if ($this->validate()) {
            if ($this->getUser() instanceof User
                && $this->getUser()->restore_password === null) {
                SendHelper::sendConfirmEmailMessage(
                    $this->email,
                    $this->getUser()->generateRestorePasswordCode(),
                    'restore_password'
                );

                $this->getUser()->save();
            }

            return true;
        }

        return false;
    }

    /**
     * @return void
     */
    public function validateRestorePasswordCode(): void
    {
        if ($this->getUser() instanceof User
            && $this->getUser()->validateRestorePassword($this->confirmEmailCode)) {
            $this->addError('', '');
        }
    }

    /**
     * @return bool
     */
    public function setNewPassword(): bool
    {
        if ($this->validate()) {
            $this->getUser()->setPassword($this->newPassword);
            $this->getUser()->restore_password = null;
            $this->getUser()->save();

            return true;
        }

        return false;
    }

    /**
     * @return User|null
     */
    private function getUser(): ?User
    {
        if ($this->_user === false) {
            $this->_user = User::findOne(['email' => $this->email]);
        }

        return $this->_user;
    }
}
