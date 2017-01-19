<?php

/* @var $this yii\web\View */
/* @var $model atans\user\models\User */

$this->title = Yii::t('user', 'Create User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-admin-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
