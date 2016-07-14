<?php
namespace frontend\models;

use Yii;
use common\models\Task;
use yii\base\Model;

/**
 * Class CreateTaskForm
 * @package frontend\models
 */
class CreateTaskForm extends Model
{
    public $title;
    public $description;
    public $user_to;
    public $priority;
    public $project_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['title', 'required'],
            ['title', 'filter', 'filter' => 'trim'],
            ['title', 'string', 'max' => 80],

            ['description', 'default', 'value' => null],

            ['priority', 'default'],

            ['user_to', 'required', 'message' => 'Choose performer.'],

            ['project_id', 'required', 'message' => 'Choose project.'],
        ];
    }

    /**
     * @return Task|null
     */
    public function createTask()
    {
        // validate signup model
        if ($this->validate()) {
            $id = Yii::$app->user->getId();
            // insert and save new task
            $task = new Task();
            $task->user_id = $id;
            $task->title = $this->title;
            $task->description = $this->description;
            $task->project_id = $this->project_id;
            $task->user_to = $this->user_to;
            $task->user_last = $this->user_to;
            $task->priority = $this->priority;
            $task->save(false);
            return $task;
        } else {
            return null;
        }
    }
}
