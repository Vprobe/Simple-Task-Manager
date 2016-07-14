<?php

use yii\db\Migration;

class m160505_180941_add_project_to_tasks extends Migration
{
    public function safeUp()
    {
        // add column project_id to task table
        $this->addColumn('{{%task}}', 'project_id', $this->integer());
        // add column project_id to task_archive table
        $this->addColumn('{{%task_archive}}', 'project_id', $this->integer());

        // add ForeignKey to task
        $this->addForeignKey(
            'FK_task_project', '{{%task}}', 'project_id', '{{%project}}', 'id', 'SET NULL', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('FK_task_project', '{{%task}}');
        $this->dropColumn('{{%task}}', 'project_id');
    }
}
