<?php

namespace atans\user\controllers;

use atans\user\traits\UserModuleTrait;
use atans\user\traits\AjaxValidationTrait;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

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
     * @throws NotFoundHttpException
     */
    public function actionChange()
    {
        if (! self::getUserModule()->enableEmailChange) {
            throw new NotFoundHttpException("Email change is disabled.");
        }

        /* @var $model \atans\user\models\forms\EmailChangeForm */
        $model = Yii::createObject([
            'class' => static::getUserModule()->modelMap['EmailChangeForm'],
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->change()) {
            Yii::$app->session->setFlash('success', Yii::t('user', "Please check your email and click the confirm link."));

            return $this->refresh();
        }

        return $this->render('change', [
            'model'      => $model,
            'userModule' => static::getUserModule(),
        ]);
    }

    /**
     * /user/email/confirm?token=xxxxxxx
     *
     * @param string $token
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionConfirm($token)
    {
        if (! self::getUserModule()->enableEmailChange) {
            throw new NotFoundHttpException("Email change is disabled.");
        }

        /* @var $userTokenModel \atans\user\models\UserToken */
        $userTokenModel = Yii::createObject([
            'class' => static::getUserModule()->modelMap['UserToken'],
        ]);

        $success = false;
        $userToken = $userTokenModel::findByToken($token, $userTokenModel::TYPE_EMAIL_CHANGE);

        if ($userToken) {
            $user = $userToken->user;

            if (! $user) {
                $userToken->delete();
                throw new ServerErrorHttpException('User does not found.');
            }

            if ($user->changeEmail($userToken->data)) {
                $success = true;
            }

            $userToken->delete();
        }

        return $this->render('confirm', [
            'success'   => $success,
            'userToken' => $userToken,
        ]);
    }
}