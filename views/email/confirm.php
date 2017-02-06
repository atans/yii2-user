<?php
use yii\helpers\Html;

/* @var $this yii\web\View  */
/* @var $userToken atans\user\models\UserToken  */
/* @var $success boolean */

$this->title = $success ? Yii::t('user', 'Confirmed') : Yii::t('user', 'Error');
?>
<div class="user-email-confirm">

    <div class="jumbotron">
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="lead">
            <?php if ($success): ?>
                <?= Yii::t('user', "Your new email {email} has been confirmed.", ["email" => $userToken->data]) ?>
            <?php else: ?>
                <?= Yii::t('user', "Invalid token") ?>
            <?php endif ?>
        </p>
        <p>
            <?= Html::a(Yii::t('user', "Go home"), Yii::$app->getHomeUrl(), ['class' => 'btn btn-lg btn-success']) ?>

        </p>
    </div>

</div>