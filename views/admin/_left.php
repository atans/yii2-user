<?php
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $model atans\user\models\User */
?>

<?= Nav::widget([
    'options' => [
        'class' => 'nav-pills nav-stacked',
    ],
    'items' => [
        [
            'label' => Yii::t('user', 'Account Details'),
            'url' => ['/user/admin/update', 'id' => $model->id]
        ],
        ['label' => Yii::t('user', 'Information'), 'url' => ['/user/admin/view', 'id' => $model->id]],
        [
            'label' => Yii::t('user', 'Assignments'),
            'url' => ['/user/admin/assignments', 'id' => $model->id],
            'visible' => isset(Yii::$app->extensions['atans/yii2-rbac']),
        ],
        '<hr>',
        [
            'label' => Yii::t('user', 'Activate'),
            'url'   => ['/user/admin/activate', 'id' => $model->id],
            'visible' => $model->status == $model::STATUS_PENDING,
            'linkOptions' => [
                'class' => 'text-danger',
                'data-method' => 'post',
                'data-confirm' => Yii::t('user', 'Are you sure you want to activate this user?'),
            ],
        ],
        [
            'label' => Yii::t('user', 'Block'),
            'url'   => ['/user/admin/block', 'id' => $model->id],
            'visible' => ! ($model->status == $model::STATUS_BLOCKED),
            'linkOptions' => [
                'class' => 'text-danger',
                'data-method' => 'post',
                'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?'),
            ],
        ],
        [
            'label' => Yii::t('user', 'Unblock'),
            'url'   => ['/user/admin/block', 'id' => $model->id],
            'visible' => $model->status == $model::STATUS_BLOCKED,
            'linkOptions' => [
                'class' => 'text-success',
                'data-method' => 'post',
                'data-confirm' => Yii::t('user', 'Are you sure you want to unblock this user?'),
            ],
        ],
        [
            'label' => Yii::t('user', 'Delete'),
            'url'   => ['/user/admin/delete', 'id' => $model->id],
            'linkOptions' => [
                'class' => 'text-danger',
                'data-method' => 'post',
                'data-confirm' => Yii::t('user', 'Are you sure you want to delete this user?'),
            ],
        ],
    ],
]) ?>