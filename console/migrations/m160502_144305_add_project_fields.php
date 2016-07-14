<?php

use yii\db\Migration;

class m160502_144305_add_project_fields extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%project}}', 'logo', $this->string(70));
        $this->addColumn('{{%project}}', 'accesses', $this->text());
        $this->addColumn('{{%project}}', 'activity', $this->boolean()->notNull()->defaultValue(1));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%project}}', 'logo');
        $this->dropColumn('{{%project}}', 'accesses');
        $this->dropColumn('{{%project}}', 'activity');
    }

}
