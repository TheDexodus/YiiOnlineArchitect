<?php

declare(strict_types=1);

namespace auth\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property User   $user
 * @property string $facebook
 * @property string $google
 */
class OAuth extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return 'oauth_users';
    }

    /**
     * {@inheritDoc}
     */
    public static function primaryKey(): array
    {
        return ['user_id'];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
