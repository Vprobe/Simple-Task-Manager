<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * TaskPriority model
 *
 * @property integer $id
 * @property string $priority_name
 */
class TaskPriority extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task_priority';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::className(), ['priority' => 'id']);
    }

}
