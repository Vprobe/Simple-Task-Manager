<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\EditProjectForm */
/* @var $project \frontend\controllers\ProjectController */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = Yii::$app->name . ' - edit project: ' . $project->name;

$symbolsLeftScript = <<<JS
    // if js active show counter
    $('.title-counter').css('display', 'inline');

    var maxSym = 30;
    $('.title-counter').text(maxSym - $('#editprojectform-name').val().length + '/30');

    $('#editprojectform-name').keyup(function() {
        if (maxSym - $(this).val().length < 0) {
            $('.title-counter').addClass('warning-cnt');
        } else {
            if ($('.title-counter').hasClass('warning-cnt')) {
                $('.title-counter').removeClass('warning-cnt')
            }
        }
        $('.title-counter').text(maxSym - $(this).val().length + '/30');
    });
JS;
$this->registerJs($symbolsLeftScript);

// yii2-redactor for accesses edit
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

<div class="project-edit">
    <h1>Edit project: <?= $project->name ?></h1>

    <p>Change the following fields to edit project:</p>

    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin(['id' => 'form-edit-project', 'options' => [
                'enctype' => 'multipart/form-data'
            ]]);
            $users = \common\models\User::find()->all();
            $items = ArrayHelper::map($users, 'id', 'name');
            ?>

            <div class="title-counter" style="display: none">30/30</div>

            <?= $form->field($model, 'name')->textInput(['value' => $project->name]) ?>
            <?= $form->field($model, 'users_id')->checkboxList($items)->label('Add users in project:'); ?>
            <?= $form->field($model, 'accesses')->textArea(['id' => 'redactor', 'value' => $project->accesses]) ?>
            <?= $form->field($model, 'activity')->checkbox()->hint('New project enable by default')->label('<b>Active</b>') ?>

            <?php if ($project->logo): ?>
                <?= Html::tag('div', '<b>Current logo:</b>')?>
            <?= Html::img('/' . $project->logo, ['style' => [
                'padding' => '2px',
                'border' => '1px solid #898989',
                'margin-bottom' => '8px'
            ]]) ?>
            <?php endif; ?>

            <?= $form->field($model, 'file')->fileInput()->label('New logo') ?>

            <div class="form-group">
                <?= Html::submitButton('Edit', ['class' => 'btn btn-primary', 'name' => 'edit-project-button']) ?>
                <?= Html::a('Cancel', ['/project/view'], ['class'=>'btn btn-warning', 'name' => 'cancel-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
