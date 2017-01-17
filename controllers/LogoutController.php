<?php

namespace atans\user\controllers;

use atans\user\traits\UserModuleTrait;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class LogoutController extends Controller
{
    use UserModuleTrait;

    const EVENT_BEFORE_LOGOUT = 'before_logout';
    const EVENT_AFTER_LOGOUT  = 'after_logout';

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
     * /user/logout
     *
     * @return \yii\web\Response
     */
    public function actionIndex()
    {
        $this->trigger(self::EVENT_BEFORE_LOGOUT);
        Yii::$app->getUser()->logout();
        $this->trigger(self::EVENT_AFTER_LOGOUT);
        
        if ($logoutRedirect = self::getUserModule()->logoutRedirect) {
            return $this->redirect($logoutRedirect);
        }

        return $this->goHome();
    }
}