<?php

use yii\db\Migration;

class m160411_175233_add_email_to_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'email', $this->string()->notNull());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'email');
    }
}
