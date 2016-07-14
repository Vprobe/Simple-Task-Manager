<?php
namespace frontend\models;

use Yii;
use common\models\Task;
use yii\base\Model;

/**
 * Class CreateTaskForm
 * @package frontend\models
 */
class UpdateTaskForm extends Model
{
    public $title;
    public $description;
    public $user_last;
    public $priority;

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

            ['user_last', 'required', 'message' => 'Choose performer.'],
        ];
    }

    /**
     * @param Task $task
     * @return Task|null
     */
    public function updateTask(Task $task)
    {
        // validate signup model
        if ($this->validate()) {
            // update task
            $task->title = $this->title;
            $task->description = $this->description;
            $task->priority = $this->priority;

            if ($task->user_last != $this->user_last) {
                $task->user_last = $this->user_last;
                $task->user_to = $this->user_last;
                if ($task->done == 1) {
                    $task->done = 0;
                }
            }

            $task->update();
            return $task;
        } else {
            return null;
        }
    }

}
