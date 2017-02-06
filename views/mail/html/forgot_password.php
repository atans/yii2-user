<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $user atans\user\models\User */
/* @var \atans\user\models\User $user */
/* @var \atans\user\models\UserToken $userToken */
/* @var $subject string */
/* @var $module atans\user\Module */

$url = Url::toRoute(["/user/password/reset", "token" => $userToken->token], true);
?>

<h3><?= Html::encode($subject) ?></h3>


<p><?= Yii::t('user', "Please use this link to reset your password:") ?></p>

<p><?= Html::a($url, $url) ?></p>
