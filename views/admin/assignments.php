<?php


use atans\rbac\widgets\Assignments;

/* @var $this yii\web\View */
/* @var $model atans\user\models\User */

$this->title = Yii::t('user', 'Assignments');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="user-admin-assignments">
    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= $this->render('_left', ['model' => $model]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="box">
                <div class="box-body">
                    <?= Assignments::widget(['user_id' => $model->id]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
