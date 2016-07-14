<?php

/* @var $this yii\web\View */
/* @var $tasksFrom frontend\controllers\SiteController */
/* @var $tasksTo frontend\controllers\SiteController */
/* @var $openTaskId frontend\controllers\SiteController */
/* @var $model frontend\controllers\SiteController */
/* @var $uCreatorId frontend\controllers\SiteController */

use yii\widgets\Pjax;
use yii\helpers\Html;
use common\models\User;
use common\models\Task;
use frontend\models\Project;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = Yii::$app->name . ' - Dashboard';

$baseUrl = Yii::$app->getUrlManager()->getBaseUrl();

// refresh all task on dashboard every minute
$tasksRefreshScript = <<< JS

// global var which contain timer function (allow clearInterval if need)
var timer;

// refresh tasks, start with page download
$(document).on('ready', function() {
    timer = setInterval(function(){
        $("#refreshButton").click();
    }, 60000);
});

// before task refreshing check comments existence, if any comment-form filling turn off the pjax event
$(document).on('pjax:beforeSend', function(event) {
    $('.field-sendcommentform-comment .form-control:visible').each(function(i, v){
        if ($(v).val().length > 0) {
            event.preventDefault();
        }
    });
});

// this functions work within pjax
$(document).on('ready pjax:complete', function() {
    task_done ();
    task_close ();
    task_revert ();
    show_desc ();
    fresh_comment_off();
});

JS;
$this->registerJs($tasksRefreshScript);

// set done true to task in db
// !need created-by class for 'created by me' column
$doneTaskScript = <<< JS
function task_done () {
    $('.task-done').on('click', function(e) {
        var btnDone = $(this);
        var taskid = $(this).data('taskid');
        $.ajax({
           url: "{$baseUrl}" + '/task/done',
           data: {taskId: taskid},
           success: function(data) {
                 if (data) {
                    // when you performer
                    btnDone.parent('span').parent('.task-row').remove();
                 } else {
                    // when you performer and creator
                    btnDone.css('display', 'none');
                    btnDone.siblings().css('display', 'inline-block');
                    btnDone.parent('span').parent('.task-row').removeClass('bg-fresh-comment').addClass('bg-task-done');
                    $('.created-by .task-title a').each(function(i, v){
                        if ($(v).data('taskid') == taskid) {
                            $(v).parent('.task-title').parent('.task-row').removeClass('bg-fresh-comment').addClass('bg-task-done');
                            return false;
                        }
                    });
                 }
           }
        });
    });
}
JS;
$this->registerJs($doneTaskScript);

// send task to archive
// !need created-by class for 'created by me' column
$closeTaskScript = <<< JS
function task_close () {
    $('.task-close').on('click', function(e) {
        var btnClose = $(this);
        var taskid = $(this).data('taskid');
        $.ajax({
           url: "{$baseUrl}" + '/task/close',
           data: {taskId: taskid},
           success: function(data) {
                 if (data) {
                    // close button exist if you creator
                    btnClose.parent('span').parent('.task-row').remove();
                    $('.created-by .task-title a').each(function(i, v){
                        if ($(v).data('taskid') == taskid) {
                            $(v).parent('.task-title').parent('.task-row').remove();
                            return false;
                        }
                    });
                 }
           }
        });
    });
}
JS;
$this->registerJs($closeTaskScript);

// revert task to performer
// !need created-by class for 'created by me' column
$revertTaskScript = <<< JS
function task_revert () {
    $('.task-revert').on('click', function(e) {
        var btnRevert = $(this);
        var taskid = $(this).data('taskid');
        $.ajax({
           url: "{$baseUrl}" + '/task/revert',
           data: {taskId: taskid},
           success: function(data) {
                 if (data) {
                    // if you performer and creator
                    btnRevert.parent('span').parent('.task-row').removeClass('bg-task-done');
                    btnRevert.css('display', 'none');
                    btnRevert.siblings('.task-done').css('display', 'inline-block');
                    btnRevert.siblings('.task-close').css('display', 'none');
                    $('.created-by .task-title a').each(function(i, v){
                        if ($(v).data('taskid') == taskid) {
                            $(v).parent('.task-title').parent('.task-row').removeClass('bg-task-done');
                            return false;
                        }
                    });
                 } else {
                    // if you creator
                    btnRevert.parent('span').parent('.task-row').remove();
                    $('.created-by .task-title a').each(function(i, v){
                        if ($(v).data('taskid') == taskid) {
                            $(v).parent('.task-title').parent('.task-row').removeClass('bg-task-done');
                            return false;
                        }
                    });
                 }
           }
        });
    });
}
JS;
$this->registerJs($revertTaskScript);

$showDescScript = <<< JS
function show_desc () {
    $('.task-title').on('click', function () {

        //if($(this).parent('.right-column').parent('.task-row').hasClass('have-desc')) {
        var to, from = false;
        var id;

        if ($(this).next().is('.task-buttons')){
            id = $(this).siblings('.task-buttons').children('a').data('taskid');
            to = true;
        } else {
            id = $(this).children('a').data('taskid');
            from = true;
        }
            $.ajax({
               url: "{$baseUrl}" + '/site/check-cookies',
               data: {taskid: id,
               to: to,
               from: from},
               success: function(data) {}
            });

        var desc = $(this).siblings('.task-body');
        if (desc.is(':visible')) {
            desc.slideToggle(400);
        } else {
            desc.slideDown(300).css('display', 'block');
        }
        //}

    });
}
JS;
$this->registerJs($showDescScript);

$undoFreshCommentScript = <<< JS
function fresh_comment_off() {
    // only for appointed column
    $('.appointed_to').children('.task-row').on('mouseover', function(){
        if($(this).hasClass('bg-fresh-comment')) {
            if ($(this).children('.task-body').is(':visible')) {
                var curRow = $(this);
                var taskid = $(this).children('.task-title').data('taskid');
                $.ajax({
                    async: false,
                    url: "{$baseUrl}" + '/task/undo-fresh-comment',
                    data: {taskId: taskid},
                    success: function(data) {
                        // remove color in appointed column
                        curRow.removeClass('bg-fresh-comment');
                        // remove color in created by me column
                        $('.created-by .task-title a').each(function(i, v){
                            if ($(v).data('taskid') == taskid) {
                                $(v).parent('.task-title').parent('.task-row').removeClass('bg-fresh-comment');
                                return false;
                            }
                        });
                    }
                })
            }
        }
    });
}
JS;
$this->registerJs($undoFreshCommentScript);

?>

<div class="body-content">

    <div class="row row-centered">

        <!-- filter by username for 'created by' -->
        <div class="col-xs-6 pull-right">
            <div class="filter-by-user">

                <?php $u = User::find()->all();
                $itemsUsers = ArrayHelper::map($u, 'id', 'name');
                foreach ($itemsUsers as $id => $val) {
                    $c = Task::getTasksQuantity($id);
                    $itemsUsers[$id] = $val . " ($c)";
                }

                $paramsUsers = [
                    'prompt' => 'Sort by user(all)',
                    'style' => ['max-width' => '130px'],
                    'onchange' => '
                    $(this).val() ? window.location = "/site/index?uCreatorId=" + $(this).val() :
                    window.location = "/site/index";'
                ];
                ?>
                <?= Html::dropDownList('filter-by-username', $uCreatorId, $itemsUsers, $paramsUsers) ?>
            </div>
        </div>

        <?php Pjax::begin(); ?>
        <?php if ($uCreatorId): ?>
            <?= Html::a("task-refresh-btn", ["site/index?uCreatorId=$uCreatorId"], ['id' => 'refreshButton', 'style' => 'display: none']) ?>
        <?php else: ?>
            <?= Html::a("task-refresh-btn", ['site/index'], ['id' => 'refreshButton', 'style' => 'display: none']) ?>
        <?php endif; ?>


        <div class="col-xs-6 pull-left">
            <div class="content text-center appointed_to">

                <div class="task-row-header text-primary"><b>Appointed to me:</b></div>

                <?php if ($tasksTo): ?>
                <?php foreach ($tasksTo as $taskTo): ?>

                <?php if ($taskTo->description): ?>
                <div class="task-row have-desc task-font
                 <?php echo $taskTo->done == 1 ? 'bg-task-done' : false ?>
                 <?php echo $taskTo->fresh_comment == 1 ? 'bg-fresh-comment' : false ?>
                  <?php echo $taskTo->priority == 1 ? 'priority-level' . $taskTo->priority. ' blink' : 'priority-level' . $taskTo->priority ?>">
                        <?php else: ?>
                            <div class="task-row task-row-padding task-font
                            <?php echo $taskTo->done == 1 ? 'bg-task-done' : false ?>
                            <?php echo $taskTo->fresh_comment == 1 ? 'bg-fresh-comment' : false ?>
                             <?php echo $taskTo->priority == 1 ? 'priority-level' . $taskTo->priority. ' blink' : 'priority-level' . $taskTo->priority ?>">
                                <?php endif; ?>

                                <!-- project title-->
                                <div class="project-title">
                                    <?php if ($taskTo->project_id): ?>
                                        <?php $proj = Project::findOne($taskTo->project_id) ?>
                                        <span class="text-primary"><?= $proj->name ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- title-->
                                <div class="task-title" data-taskid="<?= $taskTo->id ?>">
                                    <a href="javascript:void(0);">
                                        <?= $taskTo->title ?>
                                    </a>
                                </div>

                                <!-- done/close/revert buttons-->
                    <span class="task-buttons">
                        <?php if ($taskTo->done == 1): ?>
                            <a class="task-revert" style="display: inline-block;" data-taskid="<?= $taskTo->id ?>">
                                <img src="<?= Yii::$app->request->baseUrl ?>/images/revert.png" width="10px" height="10px"></a>
                            <a class="task-done" style="display: none;" data-taskid="<?= $taskTo->id ?>">
                                <img src="<?= Yii::$app->request->baseUrl ?>/images/done.png" width="10px" height="10px"></a>
                            <a class="task-close" style="display: inline-block;" data-taskid="<?= $taskTo->id ?>">
                                <img src="<?= Yii::$app->request->baseUrl ?>/images/close.png" width="10px" height="10px"></a>
                        <?php else: ?>
                            <a class="task-revert" style="display: none;" data-taskid="<?= $taskTo->id ?>">
                                <img src="<?= Yii::$app->request->baseUrl ?>/images/revert.png" width="10px" height="10px"></a>
                            <a class="task-done" style="display: inline-block;" data-taskid="<?= $taskTo->id ?>">
                                <img src="<?= Yii::$app->request->baseUrl ?>/images/done.png" width="10px" height="10px"></a>
                            <a class="task-close" style="display: none;" data-taskid="<?= $taskTo->id ?>">
                                <img src="<?= Yii::$app->request->baseUrl ?>/images/close.png" width="10px" height="10px"></a>
                        <?php endif; ?>
                    </span>

                            <!--body consist of discription, comments and comment form-->
                            <?php if (in_array($taskTo->id, $openTaskId['to'])): ?>
                            <div class="task-body" style="display: block;padding-top: 5px;">
                                <?php else: ?>
                                <div class="task-body" style="display: none;padding-top: 5px;">
                                    <?php endif; ?>

                                    <div class="left-column">
                                        <?php $creator = User::findOne($taskTo->user_id); ?>
                                            <span style="color: darkblue;"><?= $creator->name ?></span>
                                            <?= ' ('.Yii::$app->formatter->asDatetime($taskTo->created_at).')' ?>
                                    </div>

                                    <div class="right-column">

                                    <!--desc-->
                                    <?php if ($taskTo->description): ?>
                                        <div class="task-desc" style="display: block">
                                            <?= $taskTo->description ?>
                                        </div>
                                    <?php endif; ?>

                                       </div> <!--right-column-->

                                    <div style="clear: both;"></div>

                                    <!--comments-->
                                    <div class="task-comments" style="display: block;">
                                    <?php if ($comments = $taskTo->comments): ?>

                                        <?php foreach ($comments as $comment): ?>
                                            <div class="task-comment">

                                            <?php if ($taskTo->user_id == $comment->user->id): ?>
                                                <?= '&#9658; ' . '<span style="color: darkblue">' .
                                                $comment->user->name . '</span>' ?>
                                                <?php else: ?>
                                                <?= '&#9658; ' . '<span style="color: darkmagenta">' .
                                                $comment->user->name . '</span>' ?>
                                            <?php endif; ?>

                                                <?= '(' . Yii::$app->formatter->asDatetime($comment->created_at) .
                                                '): ' . $comment->comment ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                        <!--comment form-->
                                        <?php $form = ActiveForm::begin(['id' => 'form-send-comment',
                                            'options' => [
                                                'style' => ['padding-top' => '8px']
                                            ]
                                        ]);?>

                                        <?= $form->field($model, 'comment')->textarea([
                                            'rows' => '1',
                                            'style' => [
                                                'max-width' => '690px',
                                                'resize' => 'vertical',
                                                ]
                                        ])->error(false)->label('Your comment:') ?>

                                        <?= $form->field($model, 'taskid')->hiddenInput(['value' => $taskTo->id])
                                            ->error(false)->label(false) ?>

                                        <div class="form-group" style="float: left;margin-bottom: 0;">
                                            <?= Html::submitButton('Send', ['class' => 'btn btn-xs btn-primary', 'name' => 'send-comment-button']) ?>
                                            <?= Html::resetButton('Cancel', ['class'=>'btn btn-xs btn-warning']) ?>
                                        </div>
                                        <div style="clear: both;"></div>
                                        <?php ActiveForm::end(); ?>


                                    </div><!-- task-comments-->

                                </div><!-- task-body-->

                </div> <!-- task-row-div-->

                    <?php endforeach; ?>
                    <?php else: ?>
                        <p>No Tasks</p>
                    <?php endif; ?>

            </div> <!-- content-->
        </div> <!-- col-->

        <div class="col-xs-6 pull-right">
            <div class="content text-center created-by">
                <div class="task-row-header text-primary"><b>Created by me:</b></div>

                <?php if ($tasksFrom): ?>
                <?php foreach ($tasksFrom as $taskFrom): ?>

            <?php if ($taskFrom->description): ?>
                <div class="task-row have-desc task-font
                 <?php echo $taskFrom->done == 1 ? 'bg-task-done' : false ?>
                 <?php echo $taskFrom->fresh_comment == 1 ? 'bg-fresh-comment' : false ?>
                  <?php echo $taskFrom->priority == 1 ? 'priority-level' . $taskFrom->priority. ' blink' : 'priority-level' . $taskFrom->priority ?>">
                    <?php else: ?>
                    <div class="task-row task-row-padding task-font
                            <?php echo $taskFrom->done == 1 ? 'bg-task-done' : false ?>
                            <?php echo $taskFrom->fresh_comment == 1 ? 'bg-fresh-comment' : false ?>
                             <?php echo $taskFrom->priority == 1 ? 'priority-level' . $taskFrom->priority. ' blink' : 'priority-level' . $taskFrom->priority ?>">
                        <?php endif; ?>

                        <!-- project title-->
                        <div class="project-title">
                            <?php if ($taskFrom->project_id): ?>
                                <?php $proj = Project::findOne($taskFrom->project_id) ?>
                                <span class="text-primary"><?= $proj->name ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- title-->
                            <div class="task-title" style="width: 75%;" >
                                <a href="javascript: void(0);"
                                   data-taskid="<?= $taskFrom->id ?>">
                                    <?= $taskFrom->title ?>
                                </a>
                            </div>

                            <?php if (in_array($taskFrom->id, $openTaskId['from'])): ?>
                            <div class="task-body" style="display: block">
                                <?php else: ?>
                                <div class="task-body" style="display: none">
                                    <?php endif; ?>

                                    <div class="left-column">
                                        <?php $performer = User::findOne($taskFrom->user_last); ?>
                                        <span style="color: darkmagenta;">to: <?= $performer->name ?></span>
                                        <?= ' ('.Yii::$app->formatter->asDatetime($taskFrom->created_at).')' ?>
                                    </div>

                                    <div class="right-column">
                                    <?php if ($taskFrom->description): ?>
                                        <div class="task-desc" style="display: block">
                                            <?= $taskFrom->description ?>
                                        </div>
                                    <?php endif; ?>
                                    </div>

                                    <div style="clear: both;"></div>

                                    <!--comments-->
                                    <div class="task-comments" style="display: block;">
                                        <?php if ($comments = $taskFrom->comments): ?>

                                            <?php foreach ($comments as $comment): ?>
                                                <div class="task-comment">

                                                    <?php if ($taskFrom->user_id == $comment->user->id): ?>
                                                        <?= '&#9658; ' . '<span style="color: darkblue">' .
                                                        $comment->user->name . '</span>' ?>
                                                    <?php else: ?>
                                                        <?= '&#9658; ' . '<span style="color: darkmagenta">' .
                                                        $comment->user->name . '</span>' ?>
                                                    <?php endif; ?>

                                                    <?= '(' . Yii::$app->formatter->asDatetime($comment->created_at) .
                                                    '): ' . $comment->comment ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div><!-- task-comments-->

                            </div><!-- task-body-->

                        </div> <!-- task-row-div-->

                <?php endforeach; ?>
                <?php else: ?>
                    <p>No Tasks</p>
                <?php endif; ?>

            </div> <!-- content-->
        </div> <!-- col-->

        <?php Pjax::end(); ?>
    </div> <!-- row-->
</div> <!-- body-->
