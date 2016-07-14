<?php

use yii\db\Migration;

class m160418_121724_table_comment extends Migration
{

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // comment
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->notNull(),
            'comment' => $this->text(),
            'author_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey(
            'FK_comment_user', '{{%comment}}', 'author_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'FK_comment_task', '{{%comment}}', 'task_id', '{{%task}}', 'id', 'CASCADE', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%comment}}');
    }

}
