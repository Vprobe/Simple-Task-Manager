<?php
namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * Class SendCommentForm
 * @package frontend\models
 */
class SendCommentForm extends Model
{
    public $comment;
    public $taskid;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['comment', 'required', 'message' => false],
            ['comment', 'filter', 'filter' => 'trim'],

            ['taskid', 'default'],
        ];
    }

    public function sendComment()
    {
        if ($this->validate()) {
            $id = Yii::$app->user->getId();
            // insert and save new task
            $comm = new Comment();
            $comm->author_id = $id;
            $comm->comment = $this->comment;
            $comm->task_id = $this->taskid;
            $comm->save(false);
            return $comm;
        } else {
            return null;
        }
    }
}
