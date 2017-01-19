<?php

namespace atans\user\controllers;

use atans\user\traits\UserModuleTrait;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PasswordController extends Controller
{
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
                    ['allow' => true, 'actions' => ['forgot', 'reset'], 'roles' => ['?']],
                ],
            ],
        ];
    }

    /**
     * /user/password/change
     *
     * @return string|\yii\web\Response
     */
    public function actionChange()
    {
        /* @var $model \atans\user\models\forms\PasswordChangeForm */
        $model = Yii::createObject([
            'class' => static::getUserModule()->modelMap['PasswordChangeForm'],
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->change()) {
            Yii::$app->session->setFlash('success', Yii::t('user', 'Password has been changed.'));
            return $this->refresh();
        }

        return $this->render('change', [
            'model' => $model,
        ]);
    }

    /**
     * /user/password/forgot
     *
     * @return string|\yii\web\Response
     */
    public function actionForgot()
    {
        /* @var $model \atans\user\models\forms\PasswordForgotForm */
        $model = Yii::createObject([
            'class' => static::getUserModule()->modelMap['PasswordForgotForm'],
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->sendForgotEmail()) {
            Yii::$app->session->setFlash('success', Yii::t('user', 'Password reset email has been sent.'));
            return $this->refresh();
        }

        return $this->render('forgot', [
            'model' => $model,
        ]);
    }
    /**
     * /user/password/forgot -> /user/password/reset
     *
     * @param $token
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionReset($token)
    {
        /* @var $userTokenModel \atans\user\models\UserToken */
        $userTokenModel = Yii::createObject([
            'class' => self::getUserModule()->modelMap['UserToken']
        ]);

        $userToken = $userTokenModel::findByToken($token, $userTokenModel::TYPE_FORGOT_PASSWORD);

        if (! $userToken) {
            throw new NotFoundHttpException('Token does not found');
        }

        /* @var $model \atans\user\models\forms\PasswordResetForm */
        $model = Yii::createObject([
            'class' => self::getUserModule()->modelMap['PasswordResetForm'],
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            /* @var $userModel \atans\user\models\User */
            $userModel = self::getUserModule()->modelMap["User"];

            /* @var $user \atans\user\models\User */
            $user = $userModel::findOne($userToken->user_id);

            $user->changePassword($model->newPassword);

            $userToken->delete();

            Yii::$app->session->setFlash('success', Yii::t('user', 'Password has been reset.'));

            return $this->refresh();
        }

        return $this->render('reset', [
            'model' => $model,
        ]);
    }
}