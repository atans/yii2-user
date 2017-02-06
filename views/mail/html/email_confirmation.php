<?php
use yii\helpers\Html;

/* @var $user atans\user\models\User */
/* @var $userToken atans\user\models\UserToken */
/* @var $subject string */
/* @var $module atans\user\Module */

$url = $userToken->getUrl();
?>

<h3><?= Html::encode($subject) ?></h3>

<p><?= Yii::t('user', "To confirm your email {email}, please click the link below:", ['email' => $userToken->data]) ?></p>

<p><?= Html::a($url, $url) ?></p>

<p><?= Yii::t('user', 'If you can not click the link, please try pasting the text into your browser.') ?></p>
