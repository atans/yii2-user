<?php
use yii\helpers\Html;

/**
 * @var string $subject
 * @var atans\user\models\User $user
 * @var atans\user\models\UserToken $userToken
 */
$url = $userToken->getUrl();
?>

<?= Html::encode($subject) ?>

<?= Yii::t('user', 'Thank you for register on {name}', ['name' => Yii::$app->name]) ?>.

<?= Yii::t("user", "To complete your registration, please click the link below:") ?>

<?= Html::a($url, $url) ?>

<?= Yii::t('user', 'If you can not click the link, please try pasting the text into your browser.') ?>