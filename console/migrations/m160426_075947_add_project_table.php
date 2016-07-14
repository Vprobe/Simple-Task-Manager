<?php

use yii\db\Migration;

class m160426_075947_add_project_table extends Migration
{

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // project
        $this->createTable('{{%project}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(30)->notNull(),
        ], $tableOptions);

        // user_project table - for many to many relationship
        $this->createTable('{{%user_project}}', [
            'user_id' => $this->integer()->notNull(),
            'project_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK_project_user', '{{%user_project}}', 'project_id', '{{%project}}', 'id', 'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'FK_user_project', '{{%user_project}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_project}}');
        $this->dropTable('{{%project}}');
    }

}
