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

<?= Html::encode($subject) ?>


<?= Yii::t("user", "Please use this link to reset your password:") ?>

<?= Html::a($url, $url) ?>
