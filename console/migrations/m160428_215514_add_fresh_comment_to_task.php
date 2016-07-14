<?php

use yii\db\Migration;

class m160428_215514_add_fresh_comment_to_task extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%task}}', 'fresh_comment', $this->boolean()->notNull()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%task}}', 'fresh_comment');
    }
}
