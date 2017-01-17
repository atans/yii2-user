<?php

use atans\user\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model atans\user\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">
    <div class="box">
        <div class="box-body">
            <?php $form = ActiveForm::begin([
                'enableAjaxValidation' => true,
                'enableClientValidation' => false,
            ]); ?>

            <?= $form->field($model, 'username')->textInput() ?>

            <?= $form->field($model, 'email')->textInput() ?>

            <?= $form->field($model, 'password')->textInput() ?>

            <?= $form->field($model, 'status')->radioList(User::getStatusItems()) ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('user', 'Submit'), ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
