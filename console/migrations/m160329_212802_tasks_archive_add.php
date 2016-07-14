<?php

use yii\db\Migration;

class m160329_212802_tasks_archive_add extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // task_archive
        $this->createTable('{{%task_archive}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'title' => $this->string(80)->notNull(),
            'description' => $this->text(),
            'user_last' => $this->integer()->notNull(),
            'user_to' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'done' => $this->boolean()->notNull()->defaultValue(0)
        ], $tableOptions);

        // add column user_last to task table
        $this->addColumn('{{%task}}', 'user_last', $this->integer()->notNull());
    }

    public function safeDown()
    {
        $this->dropTable('{{%task_archive}}');
        $this->dropColumn('{{%task}}', 'user_last');
    }

}
