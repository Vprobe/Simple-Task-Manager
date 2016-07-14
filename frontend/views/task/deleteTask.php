<?php

/* @var $this yii\web\View */
/* @var $task common\models\Task */
/* @var $cancelBtn common\models\Task */

use yii\helpers\Html;

$this->title = 'Delete task: #' . $task->id;

?>

<div class="task-delete">

    <div class="row">
        <div class="col-lg-8">

            <p>Are you sure that you want to delete ?</p>
            <?= Html::tag('h3', Html::encode('#' . $task->id . ' - ' . $task->title), ['class' => 'text-primary']) ?>
            <br>
            <?= Html::a('Delete', ['/task/delete', 'taskId' => $task->id, 'delete' => true],
                ['class' => 'btn btn-danger', 'name' => 'delete-task-button']) ?>
            <?= Html::a('Cancel', [$cancelBtn],
                ['class' => 'btn btn-warning', 'name' => 'cancel-button']) ?>

        </div>

    </div>
</div>