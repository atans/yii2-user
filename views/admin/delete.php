<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model atans\user\models\User */

$this->title = Yii::t('user', 'Delete User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= Html::a('Delete', Url::current(), [
    'class' => 'btn btn-primary',
    'data-method' => 'post',
]) ?>