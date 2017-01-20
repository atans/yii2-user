<?php

use yii\widgets\DetailView;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $user atans\user\models\User */
/* @var $module atans\user\Module */


$this->title = Yii::t('user', 'Account');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-register-resend">

    <div class="box box-primary">
        <div class="box-header"><?= Html::encode($this->title) ?></div>
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $user,
                'attributes' => [
                    'id',
                    'username',
                    'email',
                    [
                        'attribute' => 'status',
                        'value' => $user->getStatusName(),
                    ],
                    'registration_ip',
                    'logged_in_ip',
                    'logged_in_at',
                    'created_at',
                    'updated_at',
                ],
            ]) ?>


        </div>
        <div class="box-footer">
            <p>
                <?= Html::a(Yii::t('user', 'Change password'), ['/user/password/change'], ['class' => 'btn btn-success']) ?>

                <?php if ($module->enableEmailChange): ?>
                <?= Html::a(Yii::t('user', 'Change email'), ['/user/email/change'], ['class' => 'btn btn-primary']) ?>
                <?php endif ?>

                <?= Html::a(Yii::t('user', 'Logout'), ['/user/password/change'], ['class' => 'btn btn-default']) ?>
            </p>
        </div>
    </div>
</div>
