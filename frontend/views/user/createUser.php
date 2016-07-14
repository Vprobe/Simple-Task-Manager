<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\CreateUserForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::$app->name . ' - Create new user';

?>

<div class="site-createuser">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to create new user:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-create-user']);

            $rolesArr = array();
            $roles = Yii::$app->getAuthManager()->getRoles();
            foreach($roles as $k => $v ) {
                $rolesArr[$v->name] = $v->name;
            }
            $params = ['prompt' => 'Specify user role',];
            ?>

                <?= $form->field($model, 'login')->textInput(['autocomplete' => 'off']); ?>
                <?= $form->field($model, 'name')->textInput(['autocomplete' => 'off']) ?>
                <?= $form->field($model, 'password')->textInput(['autocomplete' => 'off']) ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'role')->dropDownList($rolesArr, $params)->label('Role'); ?>

                <div class="form-group">
                    <?= Html::submitButton('Create', ['class' => 'btn btn-primary', 'name' => 'createuser-button']) ?>
                    <?= Html::a('Cancel', ['/'], ['class'=>'btn btn-warning', 'name' => 'cancel-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
