<?php

declare(strict_types=1);

namespace auth\models;

use DateTime;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii2mod\rbac\models\AuthItemModel;

/**
 * @property int           $id
 * @property string        $first_name
 * @property string        $last_name
 * @property string        $email
 * @property string        $password
 * @property string        $gender
 * @property string        $phone
 * @property DateTime      $birthday
 * @property string        $auth_key
 * @property string        $access_token
 * @property string        $confirm_email
 * @property string        $confirm_phone
 * @property string        $restore_password
 * @property string        $change_email
 * @property string        $change_phone
 * @property OAuth         $oAuth
 * @property SmsRequest    $smsRequest
 * @property AuthItemModel $authItems
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const GENDER_MALE   = 'male';
    public const GENDER_FEMALE = 'female';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'users';
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getAuthItems(): ActiveQuery
    {
        return $this->hasMany(AuthItem::class, ['name' => 'item_name'])
            ->viaTable('auth_assignment', ['user_id' => 'id'])
            ;
    }

    /**
     * @return ActiveQuery
     */
    public function getSmsRequest(): ActiveQuery
    {
        return $this->hasOne(SmsRequest::class, ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOAuth(): ActiveQuery
    {
        return $this->hasOne(OAuth::class, ['user_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id): ?User
    {
        $user = static::findOne(['id' => $id]);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null): ?User
    {
        return static::findOne(['access_token' => $token]);
    }

    public function test()
    {
        $a = 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        if ($password === $this->password) {
            return;
        }

        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    public function validateConfirmEmail(string $code): bool
    {
        if ($this->confirm_email === $code) {
            $this->confirm_email = null;

            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function generateConfirmEmailCode(): string
    {
        return ($this->confirm_email = sha1(microtime()));
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    public function validateConfirmPhone(string $code): bool
    {
        if ($this->confirm_phone === $code) {
            $this->confirm_phone = null;

            return true;
        }

        return false;
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    public function validateRestorePassword(string $code): bool
    {
        return $this->restore_password === $code;
    }

    /**
     * @return int
     */
    public function generateConfirmPhoneCode(): int
    {
        return ($this->confirm_phone = rand(1000, 9999));
    }

    /**
     * @return string
     */
    public function generateRestorePasswordCode(): string
    {
        return ($this->restore_password = sha1((string) (microtime(true) + rand(23092002, 23042020))));
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * @return string
     */
    public function generateAuthKey(): string
    {
        return ($this->auth_key = sha1(microtime()));
    }
}
