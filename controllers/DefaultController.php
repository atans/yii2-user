<?php

namespace atans\user\controllers;

use atans\user\models\LoginForm;
use atans\user\models\RegisterForm;
use atans\user\traits\ModuleTrait;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function actionRegister()
    {
        if (! $this->getModule()->enableRegistration) {
            throw new NotFoundHttpException();
        }

        /** @var $model RegisterForm */
        $model = Yii::createObject(RegisterForm::className());

        return $this->render('register', [
            'model'  => $model,
        ]);
    }

    public function actionLogin()
    {
        if (! Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        /** @var $model LoginForm */
        $model = Yii::createObject(LoginForm::className());

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->getUser()->logout();
        return $this->goHome();
    }
}