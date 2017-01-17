<?php
use yii\helpers\Html;
/**
 * @var yii\web\View $this
 * @var bool $success
 * @var string $email
 */
$this->title = Yii::t('user', $success ? 'Confirmed' : 'Error');
?>
<div class="user-confirmation-confirm">

    <div class="jumbotron">
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="lead">
            <?php if ($success): ?>
                <?= Yii::t("user", "Your email {email} has been confirmed", ["email" => $email]) ?>
            <?php else: ?>
                <?= Yii::t("user", "Invalid token") ?>
            <?php endif ?>
        </p>
        <p>
            <?= Html::a(Yii::t("user", "Go home"), Yii::$app->getHomeUrl(), ['class' => 'btn btn-lg btn-success']) ?>

            <?php if ($success && Yii::$app->user->getIsGuest()): ?>
                <?= Html::a(Yii::t("user", "Login"),['/user/login'], ['class' => 'btn btn-lg btn-success']) ?>
            <?php endif ?>
        </p>
    </div>

</div>