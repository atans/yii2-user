<?php

namespace atans\user\controllers;

use atans\user\Mailer;
use atans\user\traits\AjaxValidationTrait;
use atans\user\traits\UserModuleTrait;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class RegisterController extends Controller
{
    use AjaxValidationTrait;
    use UserModuleTrait;

    const EVENT_BEFORE_REGISTER = 'before_register';
    const EVENT_AFTER_REGISTER  = 'after_register';

    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * RegisterController constructor.
     *
     * @param string $id
     * @param \yii\base\Module $module
     * @param Mailer $mailer
     * @param array $config
     */
    public function __construct($id, $module, Mailer $mailer,  array $config = [])
    {
        $this->mailer = $mailer;

        parent::__construct($id, $module, $config);
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['index', 'confirm', 'resend'], 'roles' => ['?']],
                ],
            ],
        ];
    }

    /**
     * /user/register
     *
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        if (! static::getUserModule()->enableRegistration) {
            throw new NotFoundHttpException('Registration is disabled');
        }

        /** @var $model \atans\user\models\forms\RegistrationForm */
        $model = Yii::createObject(self::getUserModule()->modelMap['RegistrationForm']);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_REGISTER);
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            $this->trigger(self::EVENT_AFTER_REGISTER);

            if (static::getUserModule()->enableConfirmation) {
                Yii::$app->session->setFlash('success', Yii::t('user', 'Your account has been created, please check your email to confirm this account.'));
            } else {
                Yii::$app->session->setFlash('success', Yii::t('user', 'Your account has been created.'));
            }

            if (self::getUserModule()->registerRedirect) {
                return $this->redirect(self::getUserModule()->registerRedirect);
            }

            return $this->goHome();
        }

        return $this->render('index', [
            'model'      => $model,
            'userModule' => static::getUserModule(),
        ]);
    }

    /**
     * /user/register/confirm?token=xxxx
     *
     * @param string $token
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionConfirm($token)
    {
        if (! self::getUserModule()->enableConfirmation) {
            throw new NotFoundHttpException('Account confirmation is disabled');
        }

        /* @var $userTokenModel \atans\user\models\UserToken */
        $userTokenModel = Yii::createObject([
            'class' => static::getUserModule()->modelMap['UserToken'],
        ]);

        $success = false;
        $userToken = $userTokenModel::findByToken($token, $userTokenModel::TYPE_CONFIRMATION);

        if ($userToken) {
            $user = $userToken->user;

            if (! $user) {
                $userToken->delete();
                throw new ServerErrorHttpException('User does not found');
            }

            if ($user->confirm()) {
                $success = true;
            }

            $userToken->delete();
        }

        return $this->render('confirm', [
            'success'   => $success,
            'userToken' => $userToken,
        ]);
    }

    /**
     * /user/register/resend
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionResend()
    {
        if (! self::getUserModule()->enableConfirmation) {
            throw new NotFoundHttpException('Account confirmation is disabled');
        }

        /* @var $model \atans\user\models\forms\ResendForm */
        $model = Yii::createObject([
            'class' => static::getUserModule()->modelMap['ResendForm'],
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->resend()) {

            Yii::$app->session->setFlash(
                'success',
                Yii::t('user', 'A message has been sent to your email. please check your email to confirm this account.')
            );
        }

        return $this->render('resend', [
            'model' => $model,
        ]);
    }
}