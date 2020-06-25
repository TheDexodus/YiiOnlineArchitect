<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m200625_114140_add_column_photo_for_materials
 */
class m200625_114140_add_column_photo_for_materials extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('materials', 'photo', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('materials', 'photo');
    }
}
