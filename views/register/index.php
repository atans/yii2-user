<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\view */
/* @var $model atans\user\models\forms\RegisterForm */
/* @var $module atans\user\Module */

$this->title                   = Yii::t('user', 'Register');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-register-index">
    <div class="row">
        <div class="col-lg-offset-3 col-lg-6">
            <div class="box box-success">
                <div class="box-header"><?= Html::encode($this->title) ?></div>
                <div class="box-body">
                    <?php $form = ActiveForm::begin([
                        'id'                     => 'registration-form',
                        'enableAjaxValidation'   => true,
                        'enableClientValidation' => false,
                    ]); ?>

                    <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                    <?= $form->field($model, 'email')->textInput() ?>

                    <?= $form->field($model, 'password')->passwordInput() ?>

                    <?= $form->field($model, 'passwordConfirm')->passwordInput() ?>

                    <div>
                        <?= Html::submitButton(Yii::t('user', 'Register'), ['class' => 'btn btn-success']) ?>

                        <?= Html::a(Yii::t('user', 'Login'), ['/user/login'], ['class' => 'btn btn-link']) ?>

                        <?php if ($module->enableConfirmation): ?>
                            <?= Html::a(Yii::t('user', 'Resend confirmation email'), ['/user/register/resend'], ['class' => 'pull-right btn btn-link']) ?>
                        <?php endif ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
