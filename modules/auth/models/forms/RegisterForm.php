<?php

namespace auth\models\forms;

use auth\components\helpers\SendHelper;
use auth\models\SmsRequest;
use auth\models\User;
use borales\extensions\phoneInput\PhoneInputValidator;
use DateTime;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Yii;
use yii\base\Model;

/**
 * Class RegisterForm
 */
class RegisterForm extends Model
{
    const SCENARIO_REGISTER      = 'register';
    const SCENARIO_CONFIRM_EMAIL = 'confirmEmail';
    const SCENARIO_ENTER_PHONE   = 'enterPhone';
    const SCENARIO_CONFIRM_PHONE = 'confirmPhone';

    /** @var string $email */
    public $email;

    /** @var string $firstName */
    public $firstName;

    /** @var string $lastName */
    public $lastName;

    /** @var string $gender */
    public $gender;

    /** @var DateTime $birthday */
    public $birthday;

    /** @var string $phone */
    public $phone;

    /** @var string $password */
    public $password;

    /** @var string $confirmEmailCode */
    public $confirmEmailCode;

    /** @var string $confirmPhoneCode */
    public $confirmPhoneCode;

    /**
     * @return array
     */
    public function scenarios(): array
    {
        return [
            self::SCENARIO_REGISTER      => [
                'email',
                'firstName',
                'lastName',
                'gender',
                'birthday',
                'password',
            ],
            self::SCENARIO_CONFIRM_EMAIL => [],
            self::SCENARIO_ENTER_PHONE   => [
                'phone',
            ],
            self::SCENARIO_CONFIRM_PHONE => [
                'confirmPhoneCode',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [
                ['email', 'firstName', 'lastName', 'gender', 'birthday', 'password'],
                'required',
                'on' => self::SCENARIO_REGISTER,
            ],
            [
                'confirmEmailCode',
                'required',
                'on' => self::SCENARIO_CONFIRM_EMAIL,
            ],
            [
                ['phone'],
                'required',
                'on' => self::SCENARIO_ENTER_PHONE,
            ],
            [
                ['confirmPhoneCode'],
                'required',
                'on' => self::SCENARIO_CONFIRM_PHONE,
            ],
            ['email', 'email'],
            ['phone', PhoneInputValidator::class],
            ['email', 'unique', 'targetClass' => User::class],
            ['password', 'validatePassword'],
            ['firstName', 'validateFirstName'],
            ['lastName', 'validateLastName'],
            ['gender', 'validateGender'],
        ];
    }

    public function validatePhone(string $attribute): void
    {
        if (!preg_match_all('/^((\+){1}91){1}[1-9]{1}[0-9]{9}$/', $this->phone)) {
            $this->addError($attribute, 'Incorrect phone');
        }
    }

    /**
     * @param string $attribute
     */
    public function validatePassword(string $attribute): void
    {
        if (!preg_match_all('/[a-z0-9!#$%&\'()*+,\\-.\\/:;<=>?@[\\\\\]^_`{|}~"]/i', $this->password)) {
            $this->addError($attribute, 'Incorrect password');
        }
    }

    /**
     * @param string $attribute
     */
    public function validateFirstName(string $attribute): void
    {
        if (!preg_match_all('/^[a-z]+$/i', $this->firstName)
            && !preg_match_all('/^[а-я]+$/i', $this->firstName)) {
            $this->addError($attribute, 'Incorrect first name ');
        }
    }

    /**
     * @param string $attribute
     */
    public function validateLastName(string $attribute): void
    {
        if (!preg_match_all('/^[a-z]+$/i', $this->lastName)
            && !preg_match_all('/^[а-я]+$/i', $this->lastName)) {
            $this->addError($attribute, 'Incorrect last name ');
        }
    }

    /**
     * @param string $attribute
     */
    public function validateGender(string $attribute): void
    {
        if (User::GENDER_MALE !== $this->gender && User::GENDER_FEMALE !== $this->gender) {
            $this->addError($attribute, 'Incorrect gender');
        }
    }

    /**
     * @param string $attribute
     */
    public function validateBirthday(string $attribute): void
    {
        if (!preg_match_all('^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$', $this->birthday)) {
            $this->addError($attribute, 'Incorrect birthday');
        }
    }

    /**
     * @return bool
     */
    public function register(): bool
    {
        if ($this->validate()) {
            $user = new User();
            $user->first_name = $this->firstName;
            $user->last_name = $this->lastName;
            $user->birthday = $this->birthday;
            $user->gender = $this->gender;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateConfirmEmailCode();
            $user->generateConfirmPhoneCode();
            $user->generateAuthKey();

            $user->save();

            $smsRequest = new SmsRequest();
            $smsRequest->request_time = date('Y-m-d H:i:s', time());
            $smsRequest->link('user', $user);
            $smsRequest->save();

            SendHelper::sendConfirmEmailMessage($user->email, $user->confirm_email);

            return true;
        }

        return false;
    }

    /**
     * @param string $email
     * @param string $code
     *
     * @return bool
     */
    public function confirmEmail(string $email, string $code): bool
    {
        if (($user = User::findOne(['email' => $email])) instanceof User && $user->validateConfirmEmail($code)) {
            $user->save();
            Yii::$app->user->login($user, 3600 * 24 * 30);

            return true;
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function confirmPhone(User $user): bool
    {
        if (!Yii::$app->params['confirm_phone'] || $user->validateConfirmPhone($this->confirmPhoneCode)) {
            $user->save();

            return true;
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return bool
     *
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function sendPhoneCode(User $user): bool
    {
        if ($this->validate()) {
            if (!Yii::$app->params['confirm_phone']) {
                $user->phone = $this->phone;
                $user->confirm_phone = null;
                $user->save();

                return true;
            }

            $answer = SendHelper::sendPhoneCode($user, $this->phone);

            if ($answer === true) {
                return true;
            }

            $this->addError('phone', $answer);
        }

        return false;
    }
}
