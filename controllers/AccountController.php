<?php

namespace atans\user\controllers;

use atans\user\traits\UserModuleTrait;
use atans\user\traits\AjaxValidationTrait;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class AccountController extends Controller
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
                    ['allow' => true, 'actions' => ['index'], 'roles' => ['@']],
                ],
            ],
        ];
    }

    /**
     * /user/account
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $user = Yii::$app->user->getIdentity();

        return $this->render('index', [
            'user'   => $user,
            'module' => static::getUserModule(),
        ]);
    }
}