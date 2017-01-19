<?php
use yii\helpers\Html;

/* @var $user atans\user\models\User */
/* @var $userToken atans\user\models\UserToken */
/* @var $subject string */
/* @var $module atans\user\Module */

$url = $userToken->getUrl();
?>

<?= Html::encode($subject) ?>

<?= Yii::t("user", "To confirm your email {email}, please click the link below:", ['email' => $userToken->data]) ?>

<?= Html::a($url, $url) ?>

<?= Yii::t('user', 'If you can not click the link, please try pasting the text into your browser.') ?>
