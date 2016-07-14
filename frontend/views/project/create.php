<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\CreateProjectForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = Yii::$app->name . ' - Create new project';

$symbolsLeftScript = <<<JS
    // if js active show counter
    $('.title-counter').css('display', 'inline');
    var maxSym = 30;

    $('#createprojectform-name').keyup(function() {
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

<div class="project-create">
    <h1>Create new project</h1>

    <p>Please fill out the following fields to create project:</p>

    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin(['id' => 'form-create-project', 'options' => [
                'enctype' => 'multipart/form-data'
            ]]);
            $users = \common\models\User::find()->all();
            $items = ArrayHelper::map($users, 'id', 'name');
            ?>

            <div class="title-counter" style="display: none">30/30</div>

            <?= $form->field($model, 'name')->textInput() ?>
            <?= $form->field($model, 'users_id')->checkboxList($items)->label('Add users in project:'); ?>
            <?= $form->field($model, 'accesses')->textArea(['id' => 'redactor'])->hint('SSH/FTP or else') ?>
            <?= $form->field($model, 'activity')->checkbox()->hint('New project enable by default')->label('Active') ?>
            <?= $form->field($model, 'file')->fileInput()->label('Logo') ?>

            <div class="form-group">
                <?= Html::submitButton('Create', ['class' => 'btn btn-primary', 'name' => 'create-project-button']) ?>
                <?= Html::a('Cancel', ['/project/view'], ['class'=>'btn btn-warning', 'name' => 'cancel-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
