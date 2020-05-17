<?php

declare(strict_types=1);

namespace auth\models\forms;

use auth\models\User;
use Yii;
use yii\base\Model;

/**
 * Class LoginForm
 */
class LoginForm extends Model
{
    /**
     * @var string $email
     */
    public $email;

    /**
     * @var string $password
     */
    public $password;

    /**
     * @var User|bool
     */
    private $_user = false;

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function validatePassword(string $attribute): bool
    {
        if (!$this->getUser() instanceof User || !$this->getUser()->validatePassword($this->password)) {
            $this->addError($attribute, 'Incorrect email or password');

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function login(): bool
    {
        if ($this->validate() && $this->getUser()->confirm_email === null) {
            return Yii::$app->user->login($this->getUser(), 3600 * 24 * 30);
        }

        return false;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        if (!$this->_user) {
            $this->_user = User::findOne(['email' => $this->email]);
        }

        return $this->_user;
    }
}
