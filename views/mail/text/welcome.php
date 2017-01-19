<?php

use yii\helpers\Html;

/* @var $user atans\user\models\User */
/* @var $userToken atans\user\models\UserToken */
/* @var $subject string */
/* @var $module atans\user\Module */
?>

<?= Html::encode($subject) ?>

<?= Yii::t('user', 'Your account has been created on {name}.', ['name' => Yii::$app->name]) ?>

<?php if ($userToken !== null): ?>
    <?php
    $url = $userToken->getUrl();
    ?>
    <?= Yii::t("user", "To complete your registration, please click the link below:") ?>

    <?= Html::a($url, $url) ?>

    <?= Yii::t('user', 'If you can not click the link, please try pasting the text into your browser.') ?>
<?php endif ?>
