<?php

use yii\helpers\Html;

/* @var $user atans\user\models\User */
/* @var $userToken atans\user\models\UserToken */
/* @var $module atans\user\Module */
/* @var $subject string */
?>

<h3><?= Html::encode($subject) ?></h3>

<p>
    <?= Yii::t('user', 'Your account has been created on {name}.', ['name' => Yii::$app->name]) ?>
</p>

<?php if ($userToken !== null): ?>
    <?php
    $url = $userToken->getUrl();
    ?>
    <p><?= Yii::t("user", "To complete your registration, please click the link below:") ?></p>

    <p><?= Html::a($url, $url) ?></p>

    <?= Yii::t('user', 'If you can not click the link, please try pasting the text into your browser.') ?>
<?php endif ?>
