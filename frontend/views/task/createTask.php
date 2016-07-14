<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\CreateTaskForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use frontend\models\Project;
use common\models\User;

$this->title = Yii::$app->name . ' - Create new task';

$symbolsLeftScript = <<<JS
    // if js active show counter
    $('.title-counter').css('display', 'inline');
    var maxSym = 80;

    $('#createtaskform-title').keyup(function() {
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

<div class="task-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to create task:</p>

    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin(['id' => 'form-create-task']);

            // get projects depending on user role
            $userId = Yii::$app->user->id;
            $roles = (Yii::$app->authManager->getRolesByUser($userId));
            foreach ($roles as $k => $v) {
                if($k == 'admin') {
                    $projects = Project::find()->all();
                } else {
                    $cUser = User::findOne($userId);
                    $projects = $cUser->projects;
                }
            }
            $itemsProject = ArrayHelper::map($projects, 'id', 'name');

            // priority dropDownList data
            $priorities = \common\models\TaskPriority::find()->all();
            $itemsPriority = ArrayHelper::map($priorities, 'id', 'priority_name');
            $paramsPriority = [
                'prompt' => 'Specify task priority',
                'options' => [ 3 => ['Selected ' => true]]
            ];

            ?>

            <div class="title-counter" style="display: none">80/80</div>

            <?= $form->field($model, 'title')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'description')->textarea(['id' => 'redactor']) ?>

            <?php echo $form->field($model, 'project_id')->dropDownList($itemsProject, [
                'prompt' => 'Specify project for task',
                'onchange' => '
                    $.post("' . Yii::$app->urlManager->createUrl('/task/refresh-performers?id=') .
                    '"+$(this).val(), function(data) {
                        $("#createtaskform-user_to").html(data);
                    });
            '])->label('Project'); ?>

            <?= $form->field($model, 'user_to')->dropDownList(array(), ['prompt' => 'Specify project first'])
                ->label('Performer'); ?>
            <?= $form->field($model, 'priority')->dropDownList($itemsPriority, $paramsPriority)->label('Priority'); ?>

            <div class="form-group">
                <?= Html::submitButton('Create', ['class' => 'btn btn-primary', 'name' => 'create-task-button']) ?>
                <?= Html::a('Cancel', ['/'], ['class'=>'btn btn-warning', 'name' => 'cancel-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
