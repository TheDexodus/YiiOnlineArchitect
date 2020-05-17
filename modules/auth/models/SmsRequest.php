<?php

declare(strict_types=1);

namespace auth\models;

use phpDocumentor\Reflection\Types\Integer;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property User    $user
 * @property integer $request_time
 * @property integer $attempt
 */
class SmsRequest extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'sms_requests';
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

    /**
     * @return int
     */
    public function getRenewTime(): int
    {
        $repeatSend = Yii::$app->params['sms_repeat_send'];

        $time = 0;

        foreach ($repeatSend as $key => $item) {
            if ($this->attempt >= (int) $key) {
                $time = $item;
            }

            if ($this->attempt == $key) {
                break;
            }
        }

        return strtotime($this->request_time) + $time;
    }
}
