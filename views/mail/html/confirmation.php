<?php
use yii\helpers\Html;

/**
 * @var string $subject
 * @var atans\user\models\User $user
 * @var atans\user\models\UserToken $userToken
 */
$url = $userToken->getUrl();
?>

<h3><?= Html::encode($subject) ?></h3>

<?= Yii::t('user', 'Thank you for register on {name}', ['name' => Yii::$app->name]) ?>.

<p><?= Yii::t('user', "To complete your registration, please click the link below:") ?></p>

<p><?= Html::a($url, $url) ?></p>

<?= Yii::t('user', 'If you can not click the link, please try pasting the text into your browser.') ?>