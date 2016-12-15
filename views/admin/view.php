<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model atans\user\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'User'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-view">
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
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'username',
                        'email',
                        [
                            'attribute' => 'status',
                            'value' => $model->getStatusName(),
                        ],
                        'registration_ip',
                        'created_at',
                        'updated_at',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>


