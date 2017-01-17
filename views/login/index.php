<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model atans\user\models\forms\LoginForm */
/* @var $userModule atans\user\Module */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title                   = Yii::t('user', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-login">
    <div class="row">
        <div class="col-lg-offset-3 col-lg-6">
            <div class="box box-primary">
                <div class="box-header"><?= Html::encode($this->title) ?></div>
                <div class="box-body">

                    <?php $form = ActiveForm::begin(['id' => 'user-login-form']) ?>

                    <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                    <?= $form->field($model, 'password')->passwordInput() ?>

                    <?= $form->field($model, 'rememberMe')->checkbox() ?>

                    <div style="margin:1em 0">
                        <?= Html::a(Yii::t('user', 'Forgot password?'), ['/user/password/forgot']) ?>
                    </div>

                    <div>
                        <?= Html::submitButton(Yii::t('user', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>

                        <?php if ($userModule->enableRegistration): ?>
                        <?= Html::a(Yii::t('user', 'Register'), ['/user/register'], ['class' => 'btn btn-link']) ?>
                        <?php endif ?>

                        <?php if ($userModule->enableConfirmation): ?>
                            <?= Html::a(Yii::t('user', 'Resend confirmation email'), ['/user/register/resend'], ['class' => 'pull-right btn btn-link']) ?>
                        <?php endif ?>
                    </div>

                    <?php ActiveForm::end() ?>

                </div>
            </div>
        </div>
    </div>
</div>
