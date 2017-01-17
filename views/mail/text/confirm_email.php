<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var string $subject
 * @var atans\user\models\User $user
 * @var atans\user\models\UserToken $userToken
 */
$url = Url::toRoute(["/user/confirm", "token" => $userToken->token], true);
?>

<?= Html::encode($subject) ?>

<?= Yii::t("user", "Please confirm your email address by clicking the link below:") ?>

<?= Html::a($url, $url) ?>