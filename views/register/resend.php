<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model atans\user\models\forms\ResendForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('user', 'Resend confirmation email');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-register-resend">

    <div class="box box-primary">
        <div class="box-header"><?= Html::encode($this->title) ?></div>
        <div class="box-body">
            <div class="row">
                <div class="col-lg-5">
                    <?php $form = ActiveForm::begin([
                        'id' => 'user-register-resend-form',
                        'enableAjaxValidation'   => false,
                        'enableClientValidation' => false,
                    ]); ?>

                    <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('user', 'Submit'), ['class' => 'btn btn-primary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
