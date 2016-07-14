<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * TaskArchive model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $description
 * @property integer $user_to
 * @property integer $user_last
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $done
 * @property integer $priority
 * @property integer $project_id
 */
class TaskArchive extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task_archive';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Task ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'description' => 'Description',
            'user_to' => 'User to',
            'user_last' => 'Last user',
            'created_at' => 'Created',
            'updated_at' => 'Updated',
            'done' => 'Done',
            'priority' => 'Task priority',
            'project_id' => 'Project',
        ];
    }
}
