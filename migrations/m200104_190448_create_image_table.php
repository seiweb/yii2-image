<?php

namespace seiweb\image\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%image}}`.
 */
class m200104190448createimagetable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%swb_image}}', [
            'id' => $this->primaryKey(),
            'model_id' => $this->integer()->notNull(),
            'model_class' => $this->char(255),
            'model_attribute' => $this->char(255),
            'mime' => $this->string(255),
            'file_name' => $this->char(64),
            'title' => $this->string(),
            'description' => $this->string(),
            'size'=>$this->integer(),
            'sort'=>$this->integer(),
            'ext'=>$this->char(4),
            'position'=>$this->char(12),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
            'version'=>$this->integer()->defaultValue(0),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%swb_image}}');
    }
}
