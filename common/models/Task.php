<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use frontend\models\Comment;
use frontend\models\Project;

/**
 * Task model
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
 * @property integer $fresh_comment
 * @property integer $project_id
 */
class Task extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
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
            'fresh_comment' => 'Fresh comment exist',
            'project_id' => 'Project',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskPriority()
    {
        return $this->hasOne(TaskPriority::className(), ['id' => 'priority']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    /**
     * Get tasks for 'Appointed to me' column.
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getTasksTo($id)
    {
        return static::find()
            ->where(['user_to' => $id])
            ->orderBy(['priority' => SORT_ASC, 'done' => SORT_DESC, 'fresh_comment' => SORT_DESC])
            ->all();
    }

    /**
     * Get tasks for 'Created be me' column.
     * @param $id
     * @param $uCreatorId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getTasksFrom($id, $uCreatorId)
    {
        if($uCreatorId) {
            // show all created tasks for specific user where this user is performer
            return static::find()
                ->where(['user_id' => $id, 'user_to' => $uCreatorId, 'user_last' => $uCreatorId])
                ->orderBy(['priority' => SORT_ASC, 'done' => SORT_DESC, 'fresh_comment' => SORT_DESC])
                ->all();

        } else {
            // show all created tasks
            return static::find()
                ->where(['user_id' => $id])
                ->orderBy(['priority' => SORT_ASC, 'done' => SORT_DESC, 'fresh_comment' => SORT_DESC])
                ->all();
        }
    }

    /**
     * Get tasks quantity for user.
     * @param $id
     * @return int|string
     */
    public static function getTasksQuantity($id)
    {
        return static::find()
            ->where(['user_to' => $id])
            ->count();
    }

    /**
     * @param Task $task
     * @return bool
     * @throws \Exception
     */
    public static function setTaskDone(Task $task)
    {
        $task->done = 1;
        if ($task->user_to == $task->user_id) {
            $performerIsCreator = false;
        } else {
            $task->user_to = $task->user_id;
            $performerIsCreator = true;
        }
        $task->fresh_comment = 0;
        $task->update();
        return $performerIsCreator;
    }

    /**
     * @param Task $task
     * @return bool
     * @throws \Exception
     */
    public static function closeTask(Task $task)
    {
        $taskArch = new TaskArchive();
        $taskArch->user_id = $task->user_id;
        $taskArch->title = $task->title;
        $taskArch->description = $task->description;
        $taskArch->user_to = $task->user_to;
        $taskArch->user_last = $task->user_last;
        $taskArch->created_at = $task->created_at;
        $taskArch->updated_at = $task->updated_at;
        $taskArch->done = $task->done;
        $taskArch->priority = $task->priority;
        $taskArch->project_id = $task->project_id;
        $taskArch->insert();
        $task->delete();
        return true;
    }

    /**
     * @param Task $task
     * @return bool
     * @throws \Exception
     */
    public static function revertTask(Task $task)
    {
        $task->done = 0;
        if ($task->user_last == $task->user_id) {
            $performerIsCreator = true;
        } else {
            $task->user_to = $task->user_last;
            $performerIsCreator = false;
        }
        $task->fresh_comment = 0;
        $task->update();
        return $performerIsCreator;
    }

    /**
     * @param Task $task
     * @return bool
     * @throws \Exception
     */
    public static function revertWithComment(Task $task)
    {
        if (Yii::$app->user->getId() != $task->user_id) {
            // if current user not task creator
            $task->user_to = $task->user_id;
        } else {
            // if current user is task creator & performer also
            if ($task->user_last != $task->user_to) {
                $task->user_to = $task->user_last;
            }
            $task->done = 0;
        }
        $task->fresh_comment = 1;
        $task->update();
        return true;
    }

    /**
     * @param Task $task
     * @return bool
     * @throws \Exception
     */
    public static function removeFreshComment(Task $task)
    {
        $task->fresh_comment = 0;
        $task->update();
        return true;
    }

}
