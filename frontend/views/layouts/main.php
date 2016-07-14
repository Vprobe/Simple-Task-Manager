<?php

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $cUserRole string */

use yii\helpers\Html;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Url;

AppAsset::register($this);

// get current user role
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
foreach ($roles as $role) {
    $cUserRole = $role->name;
};

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">

    <div class="container">
        <?php if (!Yii::$app->user->isGuest): ?>
        <div class="site-index">

            <div class="pull-left">
                <div class="btn-group">
                    <a class="btn btn-sm btn-default" href="<?= Yii::$app->homeUrl ?>">Dashboard</a>
                </div>

                <div class="btn-group">
                    <?php if (Yii::$app->user->can('createProject')): ?>
                    <a class="btn btn-sm btn-info" href="<?= Yii::$app->request->baseUrl ?>/project/create">Create project</a>
                    <?php endif; ?>
                    <a class="btn btn-sm btn-info" href="<?= Yii::$app->request->baseUrl ?>/project/view">View projects</a>
                </div>

            </div>

            <div class="pull-right" ">
            <div class="btn-group">
                <a class="btn btn-sm btn-primary" href="<?= Yii::$app->request->baseUrl ?>/task/create">Create task</a>
            </div>

            <?php if (Yii::$app->user->can('crudUser')): ?>
                <div class="btn-group">
                    <a class="btn btn-sm btn-warning" href="<?= Url::toRoute(['/user/create-user'])?>">Create user</a>
                    <a class="btn btn-sm btn-success" href="<?= Url::toRoute(['/user/view-users'])?>">View users</a>
                </div>
            <?php endif; ?>

            <div class="btn-group">
                <a class="btn btn-sm btn-default" href="<?= Url::toRoute(['user/profile',
                    'userid' => Yii::$app->user->getId()]); ?>">Profile</a>
                <a class="btn btn-sm btn-default" href="<?= Url::toRoute(['/site/logout'])?>">
                    <?php echo 'Logout (' . Yii::$app->user->identity->login . ' | ' . Yii::$app->user->identity->name . '
                 | ' . $cUserRole . ')'?>
                </a>
            </div>
        </div>

        <div style="clear: both"></div>

    </div>

    <?php endif; ?>

    <div class="alert-wrapper" style="text-align: center; height: 33px;">
        <?= Alert::widget(['closeButton' => false]) ?>
    </div>

        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
