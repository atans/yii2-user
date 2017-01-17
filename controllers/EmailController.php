<?php

namespace atans\user\controllers;

use atans\user\traits\UserModuleTrait;
use atans\user\traits\AjaxValidationTrait;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class EmailController extends Controller
{
    use AjaxValidationTrait;
    use UserModuleTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['change'], 'roles' => ['@']],
                    ['allow' => true, 'actions' => ['confirm'], 'roles' => ['?', '@']],
                ],
            ],
        ];
    }

    /**
     * /user/email/change
     *
     * @return string|\yii\web\Response
     */
    public function actionChange()
    {
        /* @var $model \atans\user\models\forms\EmailChangeForm */
        $model = Yii::createObject([
            'class' => static::getUserModule()->modelMap['EmailChangeForm'],
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->change()) {

        }

        return $this->render('index', [
            'model'      => $model,
            'userModule' => static::getUserModule(),
        ]);
    }

    /**
     * /user/email/confirm
     *
     * @param string $token
     */
    public function actionConfirm($token)
    {
        
    }
}