<?php

namespace atans\user\controllers;

use atans\user\traits\UserModuleTrait;
use atans\user\traits\AjaxValidationTrait;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class LoginController extends Controller
{
    use AjaxValidationTrait;
    use UserModuleTrait;

    const EVENT_BEFORE_LOGIN = 'before_login';
    const EVENT_AFTER_LOGIN  = 'after_login';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['index'], 'roles' => ['?']],
                ],
            ],
        ];
    }

    /**
     * /user/login
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        if (! Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        /** @var $model \atans\user\models\forms\LoginForm */
        $model = Yii::createObject(self::getUserModule()->modelMap['LoginForm']);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_LOGIN);
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $this->trigger(self::EVENT_AFTER_LOGIN);

            return $this->goBack();
        }

        return $this->render('index', [
            'model'      => $model,
            'userModule' => static::getUserModule(),
        ]);
    }
}