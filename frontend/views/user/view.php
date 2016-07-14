<?php

/* @var $this yii\web\View */
/* @var $users frontend\controllers\UserController */

use common\models\Task;

$this->title = Yii::$app->name . ' - View users';

$baseUrl = Yii::$app->getUrlManager()->getBaseUrl();

$showDescScript = <<< JS
function show_desc () {
    $('.task-title').on('click', function () {

        //if($(this).parent('.right-column').parent('.task-row').hasClass('have-desc')) {
        /*var to, from = false;
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
            });*/

        var desc = $(this).siblings('.task-body');
        if (desc.is(':visible')) {
            desc.slideToggle(400);
        } else {
            desc.slideDown(300).css('display', 'block');
        }
        //}

    });
}

$(document).on('ready', function() {
    show_desc ();
    block_user ();
    unblock_user ();
});
JS;
$this->registerJs($showDescScript);

$blockUserScript = <<< JS
function block_user () {
    $('.user-block').on('click', function(e) {
        var isConfToBlock = confirm("Are you sure you want to block this user ?");
        if (isConfToBlock) {
            var btnBlock = $(this);
            var userid = $(this).data('userid');
            $.ajax({
               url: "{$baseUrl}" + '/user/block',
               data: {userId: userid},
               success: function(data) {
                     if (data) {
                        btnBlock.css('display', 'none');
                        btnBlock.siblings('.user-unblock').css('display', 'inline-block')
                     }
               }
            });
        }

    });
}
JS;
$this->registerJs($blockUserScript);

$unblockUserScript = <<< JS
function unblock_user () {
    $('.user-unblock').on('click', function(e) {
        var isConfToUnblock = confirm("Are you sure you want to unblock this user ?");
        if (isConfToUnblock) {
            var btnUnblock = $(this);
            var userid = $(this).data('userid');
            $.ajax({
               url: "{$baseUrl}" + '/user/unblock',
               data: {userId: userid},
               success: function(data) {
                     if (data) {
                        btnUnblock.css('display', 'none');
                        btnUnblock.siblings('.user-block').css('display', 'inline-block')
                     }
               }
            });
        }

    });
}
JS;
$this->registerJs($unblockUserScript);

?>

<div class="body-content">

    <div class="row row-centered">

        <div class="col-xs-6 col-lg-offset-3">
            <div class="content text-center all-users">
                <div class="task-row-header text-primary"><b>Users list:</b></div>

                <?php if ($users): ?>
                    <?php foreach ($users as $user): ?>

                        <div class="task-row task-font">

                            <div class="project-title" style="padding-top: 2px;padding-bottom: 2px;">
                                Tasks: <?= Task::getTasksQuantity($user->id) ?>
                                <br>
                                Projects: <?= count($user->projects) ?>
                            </div>

                            <div class="task-title">
                                <a href="javascript:void(0);" data-userid="<?= $user->id ?>">
                                    <?php $roles = array();
                                    foreach (Yii::$app->authManager->getRolesByUser($user->id) as $k => $v) {
                                        $roles[] = $k;
                                    }
                                    $roles = implode(", ", $roles); ?>
                                    <?= $user->name . " ($roles)"?>
                                </a>
                            </div>

                        <!-- edit/active buttons-->
                        <span class="task-buttons">
                                <a data-userid="<?= $user->id ?>" class="user-block"
                                   style="display: <?php echo $user->block == 0 ? 'inline-block' : 'none'; ?>">
                                    <img src="<?= Yii::$app->request->baseUrl ?>/images/done.png" width="10px" height="10px">
                                </a>
                                <a data-userid="<?= $user->id ?>" class="user-unblock"
                                   style="display: <?php echo $user->block == 0 ? 'none' : 'inline-block'; ?>;">
                                    <img src="<?= Yii::$app->request->baseUrl ?>/images/block.png" width="10px" height="10px">
                                </a>
                        </span>

                            <div class="task-body" style="display: none;padding-top: 5px;">

                                <div class="left-column"></div>

                                <div class="right-column">
                                    <div class="task-desc" style="display: block;">
                                        <?php if ($user->email): ?>
                                            Email: <?= $user->email ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div style="clear: both;"></div>

                            </div><!-- task-body-->

                        </div> <!-- task-row-div-->

                    <?php endforeach; ?>
                <?php else: ?>
                    <p>There is no users here.</p>
                <?php endif; ?>

            </div> <!-- content-->
        </div> <!-- col-->


    </div> <!-- row-->
</div> <!-- body-->
