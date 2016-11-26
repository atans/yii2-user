<?php

namespace atans\user\controllers;

use atans\user\traits\ModuleTrait;
use atans\user\traits\AjaxValidationTrait;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{
    const EVENT_BEFORE_LOGIN = 'before_login';
    const EVENT_AFTER_LOGIN  = 'after_login';

    const EVENT_BEFORE_REGISTER = 'before_register';
    const EVENT_AFTER_REGISTER  = 'after_register';

    const EVENT_BEFORE_LOGOUT = 'before_logout';
    const EVENT_AFTER_LOGOUT = 'after_logout';

    use AjaxValidationTrait;
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['login', 'register', ''], 'roles' => ['?']],
                    ['allow' => true, 'actions' => ['logout'], 'roles' => ['@']],
                ],
            ],
        ];
    }


    public function actionLogin()
    {
        if (! Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        /** @var $model \atans\user\models\LoginForm */
        $model = Yii::createObject($this->getModule()->modelMap['LoginForm']);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_LOGIN);
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $this->trigger(self::EVENT_AFTER_LOGIN);

            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionRegister()
    {
        if (! $this->getModule()->enableRegistration) {
            throw new NotFoundHttpException();
        }

        /** @var $model \atans\user\models\RegistrationForm */
        $model = Yii::createObject($this->getModule()->modelMap['RegistrationForm']);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_REGISTER);
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            $this->trigger(self::EVENT_AFTER_REGISTER);

            Yii::$app->session->setFlash('success', Yii::t('user', 'Your account has been created.'));

            if ($this->getModule()->redirectUrlAfterRegistration) {
                return $this->redirect($this->getModule()->redirectUrlAfterRegistration);
            }

            return $this->goHome();
        }

        return $this->render('register', [
            'model'  => $model,
        ]);
    }

    public function actionLogout()
    {
        $this->trigger(self::EVENT_BEFORE_LOGOUT);
        Yii::$app->getUser()->logout();
        $this->trigger(self::EVENT_AFTER_LOGOUT);

        return $this->goHome();
    }
}