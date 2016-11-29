<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\view */
/* @var $model atans\user\models\RegistrationForm */

$this->title = Yii::t('user', 'Register');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-login">
    <h3><?= Html::encode($this->title) ?></h3>

    <div class="row">
        <div class="col-lg-5">
        <?php $form = ActiveForm::begin([
            'id'                     => 'registration-form',
            'enableAjaxValidation'   => true,
            'enableClientValidation' => false,
        ]); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'email')->textInput() ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'passwordRepeat')->passwordInput() ?>


            <?= Html::submitButton(Yii::t('user', 'Register'), ['class' => 'btn btn-success']) ?>

        <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
