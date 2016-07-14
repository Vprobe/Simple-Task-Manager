<?php

use yii\db\Migration;

class m160328_085040_add_date_create_and_update_to_task extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%task}}', 'created_at', $this->integer()->notNull());
        $this->addColumn('{{%task}}', 'updated_at', $this->integer()->notNull());
        $this->addColumn('{{%task}}', 'done', $this->boolean()->notNull()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%task}}', 'created_at');
        $this->dropColumn('{{%task}}', 'updated_at');
        $this->dropColumn('{{%task}}', 'done');
    }
}
