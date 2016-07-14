<?php

/* @var $this yii\web\View */
/* @var $projects frontend\controllers\SiteController */

use yii\helpers\Url;

$this->title = Yii::$app->name . ' - View projects';

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
});
JS;
$this->registerJs($showDescScript);

?>

<div class="body-content">

    <div class="row row-centered">

        <div class="col-xs-6 col-lg-offset-3">
            <div class="content text-center all-projects">
                <div class="task-row-header text-primary"><b>Current projects:</b></div>

                <?php if ($projects): ?>
                    <?php foreach ($projects as $project): ?>

                        <div class="task-row task-font">

                            <div class="project-title" style="padding-top: 2px;padding-bottom: 2px;"></div>

                            <div class="task-title">
                                <a href="javascript:void(0);" data-taskid="<?= $project->id ?>">
                                    <?= $project->name ?>
                                </a>
                            </div>

                        <!-- edit/active buttons-->
                        <span class="task-buttons">
                                <span class="project-active" style="display: inline-block;">
                                    <img src="<?= Yii::$app->request->baseUrl ?>/images/done.png" width="10px" height="10px">
                                </span>
                            <?php if (Yii::$app->user->can('createProject')): ?>
                                <a href="<?= Url::toRoute(['/project/edit', 'projectId' => $project->id]) ?>"
                                   class="project-edit" style="display: inline-block;">
                                    <img src="<?= Yii::$app->request->baseUrl ?>/images/edit.png" width="10px" height="10px">
                                </a>
                            <?php endif; ?>
                        </span>

                            <div class="task-body" style="display: none;padding-top: 5px;">

                                <div class="left-column"></div>

                                <div class="right-column">
                                    <div class="task-desc" style="display: block;">
                                        <?= $project->accesses ?>
                                    </div>
                                </div>

                                <div style="clear: both;"></div>

                            </div><!-- task-body-->

                        </div> <!-- task-row-div-->

                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No projects here. Ask your admin or PM to get access to the specific project.</p>
                <?php endif; ?>

            </div> <!-- content-->
        </div> <!-- col-->


    </div> <!-- row-->
</div> <!-- body-->
