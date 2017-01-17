<?php

namespace atans\user\controllers;

use atans\user\traits\UserModuleTrait;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ServerErrorHttpException;

class ConfirmationController extends Controller
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
                    ['allow' => true, 'actions' => ['index'], 'roles' => ['@']],
                ],
            ],
        ];
    }

    /**
     * /user/confirmation/confirm?token=xxxx
     *
     * @param string $token
     * @return \yii\web\Response
     * @throws ServerErrorHttpException
     */
    public function actionActivate($token)
    {
        /* @var $userTokenModel \atans\user\models\UserToken */
        $userTokenModel = Yii::createObject([
            'class' => static::getUserModule()->modelMap['UserToken'],
        ]);

        $success = false;
        $email = null;

        $userToken = $userTokenModel::findByToken(
            $token,
            [$userTokenModel::TYPE_CONFIRMATION, $userTokenModel::TYPE_EMAIL_CHANGE]
        );

        if ($userToken) {
            $user = $userToken->user;
            if (! $user) {
                throw new ServerErrorHttpException('User does not found');
            }

            $newEmail = $userToken->data;
            if ($user->confirm($newEmail)) {
                $success = true;
            }

            $email = $newEmail ? $newEmail : $user->email;
            $userToken->delete();
        }

        return $this->render('confirm', [
            'success'   => $success,
            'userToken' => $userToken,
            'email'     => $email,
        ]);
    }
}