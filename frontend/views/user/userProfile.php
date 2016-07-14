<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\UserProfileForm */
/* @var $user \common\models\User */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::$app->name . ' - Profile #' . $user->id . ' : ' . $user->name;

?>

<div class="user-profile">
    <h1>Profile #<?= $user->id ?> - <?= $user->name ?></h1>

    <p>You can change the following fields to update your profile:</p>

    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin(['id' => 'form-user-profile']); ?>

            <?= $form->field($model, 'login')->textInput(['value' => $user->login]); ?>
            <?= $form->field($model, 'name')->textInput(['value' => $user->name]); ?>
            <?= $form->field($model, 'password')->passwordInput(); ?>

            <div class="form-group">
                <?= Html::submitButton('Update', ['class' => 'btn btn-primary', 'name' => 'update-user-button']) ?>
                <?= Html::a('Cancel', ['/'], ['class'=>'btn btn-warning']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
