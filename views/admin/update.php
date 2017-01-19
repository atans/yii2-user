<?php

/* @var $this yii\web\View */
/* @var $model atans\user\models\User */

$this->title = Yii::t('user', 'Update User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-admin-update">
    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= $this->render('_left', ['model' => $model]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
