<?php

namespace app\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200505_081442_create_admin
 */
class m200505_081442_create_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            'users',
            [
                'id'         => 0,
                'email'      => Yii::$app->params['admin_email'],
                'first_name' => 'Admin',
                'last_name'  => '',
                'birthday'   => date('Y-m-d'),
                'password'   => password_hash(Yii::$app->params['admin_password'], PASSWORD_BCRYPT),
                'gender'     => 'male',
                'auth_key'   => sha1(microtime()),
            ]
        );

        $this->insert('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_VIEW_ALL_MODELS']);
        $this->insert('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_USER_VIEW']);
        $this->insert('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_USER_CREATE']);
        $this->insert('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_USER_UPDATE']);
        $this->insert('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_USER_DELETE']);
        $this->insert('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_TYPE_VIEW']);
        $this->insert('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_TYPE_CREATE']);
        $this->insert('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_TYPE_UPDATE']);
        $this->insert('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_TYPE_DELETE']);
        $this->insert('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_VIEW']);
        $this->insert('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_CREATE']);
        $this->insert('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_UPDATE']);
        $this->insert('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_DELETE']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_VIEW_ALL_MODELS']);
        $this->delete('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_USER_VIEW']);
        $this->delete('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_USER_CREATE']);
        $this->delete('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_USER_UPDATE']);
        $this->delete('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_USER_DELETE']);
        $this->delete('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_TYPE_VIEW']);
        $this->delete('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_TYPE_CREATE']);
        $this->delete('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_TYPE_UPDATE']);
        $this->delete('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_TYPE_DELETE']);
        $this->delete('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_VIEW']);
        $this->delete('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_CREATE']);
        $this->delete('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_UPDATE']);
        $this->delete('auth_assignment', ['user_id' => 0, 'item_name' => 'ROLE_MATERIAL_DELETE']);

        $this->delete('users', ['email' => Yii::$app->params['admin_email']]);
    }
}
