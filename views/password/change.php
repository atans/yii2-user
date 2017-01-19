<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model atans\user\models\forms\PasswordChangeForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('user', 'Change Password');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-password-change">
    <div class="box">
        <div class="box-header"><?= Html::encode($this->title) ?></div>
        <div class="box-body">
            <div class="row">
                <div class="col-lg-5">
                    <?php $form = ActiveForm::begin([
                        'id' => 'password-change-form',
                        'enableAjaxValidation'   => false,
                        'enableClientValidation' => false,
                    ]); ?>

                    <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>

                    <?= $form->field($model, 'newPassword')->passwordInput() ?>

                    <?= $form->field($model, 'newPasswordConfirm')->passwordInput() ?>

                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('user', 'Submit'), ['class' => 'btn btn-primary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
