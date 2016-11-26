<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model atans\user\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Banks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="bank-view">

    <p>
        <?= Html::a(Yii::t('user', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <div class="box">
        <div class="box-body">
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
