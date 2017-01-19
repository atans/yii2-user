<?php

use atans\user\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel atans\user\models\Search\UserSearch */
/* @var $model atans\user\models\User */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('user', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-admin-index">

    <p>
        <?= Html::a(Yii::t('user', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= $this->render('_menu') ?>

    <div class="box">
        <?= GridView::widget([
            'summaryOptions' => ['class' => 'box-header'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'id',
                'username',
                'email',
                'registration_ip',
                'logged_in_ip',
                'logged_in_at',
                [
                    'attribute' => 'status',
                    'format'    => 'raw',
                    'value' => function($model){
                        /* @var $model \atans\user\models\User */

                        switch ($model->status) {
                            case User::STATUS_ACTIVE:
                                $class = 'label-success';
                                break;
                            case User::STATUS_BLOCKED:
                                $class = 'label-danger';
                                break;
                            default:
                                $class = 'label-default';
                                break;
                        }

                        return Html::tag('span', $model->getStatusName(), ['class' => 'label ' . $class]);
                    },
                ],
                //'created_at',
                //'updated_at',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('user', 'Actions'),
                ],
            ],
        ]); ?>
    </div>
</div>
