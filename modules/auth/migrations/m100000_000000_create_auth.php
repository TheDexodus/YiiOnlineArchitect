<?php

namespace auth\migrations;

use yii\db\Migration;

/**
 * Class m100000_000000_create_auth
 */
class m100000_000000_create_auth extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            'users',
            [
                'id'               => $this->primaryKey(),
                'email'            => $this->string()->notNull(),
                'first_name'       => $this->string()->notNull(),
                'last_name'        => $this->string()->notNull(),
                'password'         => $this->string()->notNull(),
                'phone'            => $this->string(),
                'gender'           => $this->string()->notNull(),
                'birthday'         => $this->date()->notNull(),
                'auth_key'         => $this->string(),
                'access_token'     => $this->string(),
                'confirm_email'    => $this->string(),
                'confirm_phone'    => $this->string(),
                'change_email'     => $this->string(),
                'change_phone'     => $this->string(),
                'restore_password' => $this->string(),
            ]
        );

        $this->createTable(
            'oauth_users',
            [
                'user_id'  => $this->integer()->unique(),
                'facebook' => $this->integer(),
                'google'   => $this->integer(),
            ]
        );

        $this->createIndex(
            'idx-oauth_users-user_id',
            'oauth_users',
            'user_id'
        );

        $this->addForeignKey(
            'fk-oauth_users-user_id',
            'oauth_users',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );

        $this->createTable(
            'sms_requests',
            [
                'user_id'      => $this->integer()->unique(),
                'request_time' => $this->timestamp()->notNull(),
                'attempt'      => $this->integer()->notNull()->defaultValue(0),
            ]
        );

        $this->createIndex(
            'idx-sms_requests-user_id',
            'sms_requests',
            'user_id'
        );

        $this->addForeignKey(
            'fk-sms_requests-user_id',
            'sms_requests',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdyioc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-sms_requests-user_id', 'sms_requests');
        $this->dropForeignKey('fk-sms_requests-user_id', 'sms_requests');

        $this->dropIndex('idx-oauth_users-user_id', 'ouath_users');
        $this->dropForeignKey('fk-oauth_users-user_id', 'oauth_users');

        $this->dropTable('sms_requests');
        $this->dropTable('oauth_users');
        $this->dropTable('users');
    }
}
