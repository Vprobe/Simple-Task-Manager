<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // user
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'login' => $this->string(20)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'name' => $this->string(20)->notNull(),
        ], $tableOptions);
        $this->createIndex('indx_name', '{{%user}}', 'name');

        // task
        $this->createTable('{{%task}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'title' => $this->string(80)->notNull(),
            'description' => $this->text(),
            'user_to' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex('indx_title', '{{%task}}', 'title');
        $this->addForeignKey(
            'FK_task_user', '{{%task}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE'
        );

        // insert users
        $this->insert('{{%user}}', [
            'id' => 1,
            'login' => 'user1',
            'password_hash' => '$2y$13$kVuz2gBKyoyt0y6hbqlcR./apNYBVmLT7m7m.c2a8Unt1pG9TmuDS',
            'name' => 'name1'
        ]);
        $this->insert('{{%user}}', [
            'id' => 2,
            'login' => 'user2',
            'password_hash' => '$2y$13$6X6R4rBG/NwhqnK2aOCf6uhxh1CAInp0pHBU31ycNwWkkjlxy4PEy',
            'name' => 'name2'
        ]);
        $this->insert('{{%user}}', [
            'id' => 3,
            'login' => 'user3',
            'password_hash' => '$2y$13$o5qMAqm1T8kW6.h/SZ3plOPlo8gXoyoZmgw.QAyefxdJK9zYc8r6S',
            'name' => 'name3'
        ]);

        // insert tasks
        $this->insert(
            '{{%task}}', [
            'id' => 1,
            'user_id' => 1,
            'title' => 'task1',
            'description' => 'desc1',
            'user_to' => 2
        ]);
        $this->insert(
            '{{%task}}', [
            'id' => 2,
            'user_id' => 2,
            'title' => 'task2',
            'description' => 'desc2',
            'user_to' => 3
        ]);
        $this->insert(
            '{{%task}}', [
            'id' => 3,
            'user_id' => 3,
            'title' => 'task3',
            'description' => 'desc3',
            'user_to' => 1
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%task}}');
        $this->dropTable('{{%user}}');
    }
}
