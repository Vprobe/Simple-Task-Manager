<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\UpdateTaskForm */
/* @var $task \common\models\Task */
/* @var $cancelBtn common\models\Task */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = 'Update task';

$symbolsLeftScript = <<<JS
    // if js active show counter
    $('.title-counter').css('display', 'inline');

    var maxSym = 80;
    $('.title-counter').text(maxSym - $('#updatetaskform-title').val().length + '/80');

    $('#updatetaskform-title').keyup(function() {
        if (maxSym - $(this).val().length < 0) {
            $('.title-counter').addClass('warning-cnt');
        } else {
            if ($('.title-counter').hasClass('warning-cnt')) {
                $('.title-counter').removeClass('warning-cnt')
            }
        }
        $('.title-counter').text(maxSym - $(this).val().length + '/80');
    });
JS;
$this->registerJs($symbolsLeftScript);

// yii2-redactor for description edit
$renderScript = <<<JS
$(function()
{
    $('#redactor').redactor({
        buttonsHide: ['html', 'deleted', 'image', 'file', 'link', 'horizontalrule'],
        minHeight: 170,
    });
});
JS;
$this->registerJs($renderScript);

?>

<div class="task-update">
    <h1><?= Html::encode($this->title . ' #' . $task->id) ?></h1>

    <p>Change the following fields to update task:</p>

    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin(['id' => 'form-update-task']);

            $users = \common\models\User::find()->all();
            $items = ArrayHelper::map($users, 'id', 'name');
            $paramsPerform = [
                'prompt' => 'Specify task performer',
                'options' => [ $task->user_last => ['Selected ' => true]]
            ];

            $priorities = \common\models\TaskPriority::find()->all();
            $itemsPrior = ArrayHelper::map($priorities, 'id', 'priority_name');
            $paramsPrior = [
                'prompt' => 'Specify task priority',
                'options' => [ $task->priority => ['Selected ' => true]]
            ];

            ?>

            <div class="title-counter" style="display: none">80/80</div>

            <?= $form->field($model, 'title')->textInput(['value' => $task->title]) ?>
            <?= $form->field($model, 'description')->textarea([
                'value' => $task->description,
                'id' => 'redactor',
                'rows' => '6']) ?>
            <?= $form->field($model, 'user_last')->dropDownList($items, $paramsPerform)->label('Performer'); ?>
            <?= $form->field($model, 'priority')->dropDownList($itemsPrior, $paramsPrior)->label('Priority'); ?>

            <div class="form-group">
                <?= Html::submitButton('Update', ['class' => 'btn btn-primary', 'name' => 'update-task-button']) ?>
                <?= Html::a('Cancel', [$cancelBtn],
                    ['class'=>'btn btn-warning']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
