<?php

use yii\db\Migration;

class m160330_131609_add_priority_to_task extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // add column priority to task table
        $this->addColumn('{{%task}}', 'priority', $this->integer()->notNull()->defaultValue(3));

        // add column priority to task_archive table
        $this->addColumn('{{%task_archive}}', 'priority', $this->integer()->notNull()->defaultValue(3));

        // task_priority
        $this->createTable('{{%task_priority}}', [
            'id' => $this->primaryKey(),
            'priority_name' => $this->string(40)->notNull(),
        ], $tableOptions);

        // insert priorities
        $this->insert('{{%task_priority}}', [
            'id' => 1,
            'priority_name' => 'achtung!',
        ]);
        $this->insert('{{%task_priority}}', [
            'id' => 2,
            'priority_name' => 'high',
        ]);
        $this->insert('{{%task_priority}}', [
            'id' => 3,
            'priority_name' => 'medium',
        ]);
        $this->insert('{{%task_priority}}', [
            'id' => 4,
            'priority_name' => 'low',
        ]);

        // add ForeignKey to task
        $this->addForeignKey(
            'FK_task_priority', '{{%task}}', 'priority', '{{%task_priority}}', 'id', 'NO ACTION', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('FK_task_priority', '{{%task}}');
        $this->dropTable('{{%task_priority}}');
        $this->dropColumn('{{%task}}', 'priority');
        $this->dropColumn('{{%task_archive}}', 'priority');
    }
}
