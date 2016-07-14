<?php

use yii\db\Migration;

class m160512_122158_add_field_for_block_user extends Migration
{

    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'block', $this->boolean()->notNull()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'block');
    }

}
