<?php
use yii\helpers\Html;

/* @var $this yii\web\View  */
/* @var $userToken atans\user\models\UserToken */
/* @var $success bool */

$this->title = $success ? Yii::t('user', 'Confirmed') : Yii::t('user', 'Error');
?>
<div class="user-register">

    <div class="jumbotron">
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="lead">
            <?php if ($success): ?>
                <?= Yii::t('user', "Your account {email} has been confirmed", ["email" => $userToken->user->email]) ?>
            <?php else: ?>
                <?= Yii::t('user', "Invalid token") ?>
            <?php endif ?>
        </p>
        <p>
            <?= Html::a(Yii::t('user', "Go home"), Yii::$app->getHomeUrl(), ['class' => 'btn btn-lg btn-success']) ?>

            <?php if ($success && Yii::$app->user->getIsGuest()): ?>
                <?= Html::a(Yii::t('user', "Login"),['/user/login'], ['class' => 'btn btn-lg btn-success']) ?>
            <?php endif ?>
        </p>
    </div>

</div>