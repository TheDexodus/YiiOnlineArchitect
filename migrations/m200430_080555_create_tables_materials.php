<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Class m200430_080555_create_tables_materials
 */
class m200430_080555_create_tables_materials extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            'material_types',
            [
                'id'                  => $this->primaryKey(),
                'display_name'        => $this->string(),
                'usage'               => $this->string(),
                'measurements'        => $this->string(),
                'typical_consumption' => $this->string(),
            ]
        );

        $this->createTable(
            'materials',
            [
                'id'           => $this->primaryKey(),
                'vendor_code'  => $this->integer()->unique()->notNull(),
                'display_name' => $this->string(),
                'type_id'      => $this->integer(),
                'use_pattern'  => $this->string()->notNull(),
                'color'        => $this->string(),
                'picture'      => $this->string(),
                'price'        => $this->float(),
                'multiplier'   => $this->float(),
                'details'      => $this->json(),
            ]
        );

        $this->createIndex(
            'idx-materials-type_id',
            'materials',
            'type_id'
        );

        $this->addForeignKey(
            'fk-materials-type_id',
            'materials',
            'type_id',
            'material_types',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-materials-type_id', 'materials');
        $this->dropIndex('idx-materials-type_id', 'materials');
        $this->dropTable('materials');
        $this->dropTable('material_types');
    }
}
