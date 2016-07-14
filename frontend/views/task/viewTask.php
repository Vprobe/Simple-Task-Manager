<?php
/* @var $this yii\web\View */
/* @var $task common\models\Task */
/* @var $uTo common\models\User */
/* @var $uCreator common\models\User */

use yii\helpers\Html;

$this->title = 'View task: #' . $task->id;
$taskPrority = $task->taskPriority;

?>

<div class="task-create">

    <div class="row">
        <div class="col-lg-8">

            <?= Html::tag('h3', Html::encode($task->title), ['class' => 'text-primary text-center']) ?>
            <?= Html::tag('h4', Html::encode('Priority: ' . $taskPrority->priority_name), ['class' => 'text-default text-right']) ?>

            <?= Html::tag('p', $task->description) ?>
            <br>
            <div class="">
                <?= Html::tag('span', Html::encode('Created by: ' . $uCreator->name),
                    ['class' => 'text-default label-info img-rounded',
                        'style' => "padding: 3px;"]) ?>
                <?= Html::tag('span', Html::encode('Performer: ' . $uTo->name),
                    ['class' => 'text-default label-success img-rounded pull-right',
                        'style' => "padding: 3px;"]) ?>
            </div>
            <br>
            <?= Html::tag(
                'span', Html::encode('Created at: ' . Yii::$app->formatter->asDatetime($task->created_at) .
                ' | ' . 'Updated at: ' . Yii::$app->formatter->asDatetime($task->updated_at)),
                ['class' => 'text-default']) ?>
            <br>
            <br>
            <?= Html::a('Update', ['/task/update', 'taskId' => $task->id],
                ['class' => 'btn btn-primary', 'name' => 'update-task-button']) ?>
            <?= Html::a('Cancel', ['/'],
                ['class' => 'btn btn-warning', 'name' => 'cancel-button']) ?>

        </div>

    </div>
</div>
