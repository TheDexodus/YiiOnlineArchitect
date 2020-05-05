<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m200505_081441_add_roles
 */
class m200505_081441_add_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('auth_item', ['type' => 1, 'name' => 'ROLE_VIEW_ALL_MODELS']);
        $this->insert('auth_item', ['type' => 1, 'name' => 'ROLE_USER_VIEW']);
        $this->insert('auth_item', ['type' => 1, 'name' => 'ROLE_USER_CREATE']);
        $this->insert('auth_item', ['type' => 1, 'name' => 'ROLE_USER_UPDATE']);
        $this->insert('auth_item', ['type' => 1, 'name' => 'ROLE_USER_DELETE']);
        $this->insert('auth_item', ['type' => 1, 'name' => 'ROLE_MATERIAL_TYPE_VIEW']);
        $this->insert('auth_item', ['type' => 1, 'name' => 'ROLE_MATERIAL_TYPE_CREATE']);
        $this->insert('auth_item', ['type' => 1, 'name' => 'ROLE_MATERIAL_TYPE_UPDATE']);
        $this->insert('auth_item', ['type' => 1, 'name' => 'ROLE_MATERIAL_TYPE_DELETE']);
        $this->insert('auth_item', ['type' => 1, 'name' => 'ROLE_MATERIAL_VIEW']);
        $this->insert('auth_item', ['type' => 1, 'name' => 'ROLE_MATERIAL_CREATE']);
        $this->insert('auth_item', ['type' => 1, 'name' => 'ROLE_MATERIAL_UPDATE']);
        $this->insert('auth_item', ['type' => 1, 'name' => 'ROLE_MATERIAL_DELETE']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('auth_item', ['name' => 'ROLE_VIEW_ALL_MODELS']);
        $this->delete('auth_item', ['name' => 'ROLE_USER_VIEW']);
        $this->delete('auth_item', ['name' => 'ROLE_USER_CREATE']);
        $this->delete('auth_item', ['name' => 'ROLE_USER_UPDATE']);
        $this->delete('auth_item', ['name' => 'ROLE_USER_DELETE']);
        $this->delete('auth_item', ['name' => 'ROLE_MATERIAL_TYPE_VIEW']);
        $this->delete('auth_item', ['name' => 'ROLE_MATERIAL_TYPE_CREATE']);
        $this->delete('auth_item', ['name' => 'ROLE_MATERIAL_TYPE_UPDATE']);
        $this->delete('auth_item', ['name' => 'ROLE_MATERIAL_TYPE_DELETE']);
        $this->delete('auth_item', ['name' => 'ROLE_MATERIAL_VIEW']);
        $this->delete('auth_item', ['name' => 'ROLE_MATERIAL_CREATE']);
        $this->delete('auth_item', ['name' => 'ROLE_MATERIAL_UPDATE']);
        $this->delete('auth_item', ['name' => 'ROLE_MATERIAL_DELETE']);
    }
}
