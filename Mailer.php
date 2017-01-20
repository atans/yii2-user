<?php

namespace atans\user;

use atans\user\models\User;
use atans\user\models\UserToken;
use atans\user\traits\UserModuleTrait;
use Yii;
use yii\base\Component;
use yii\mail\BaseMailer;

class Mailer extends Component
{
    use UserModuleTrait;

    /* @var BaseMailer */
    protected $mailer;
    
    /** @var string */
    public $viewPath = '@atans/user/views/mail';

    /** @var string|array Default: `Yii::$app->params['adminEmail']` OR `no-reply@example.com` */
    public $sender;

    /** @var string */
    protected $welcomeSubject;

    /** @var string */
    protected $confirmationSubject;

    /** @var string */
    protected $emailConfirmationSubject;

    /** @var string */
    protected $forgotPasswordSubject;

    /**
     * Send welcome message
     * From: /user/register
     *
     * @param User $user
     * @param UserToken|null $userToken
     * @return bool
     */
    public function sendWelcomeMessage(User $user, UserToken $userToken = null)
    {
        $subject= $this->getWelcomeSubject();

        return $this->sendMessage(
            $user->email,
            $this->getWelcomeSubject(),
            'welcome',
            ['user' => $user, 'userToken' => $userToken, 'subject' => $subject, 'module' => self::getUserModule()]
        );
    }

    /**
     * Send confirmation message
     * From: /user/register/resend
     *
     * @param User $user
     * @param UserToken $userToken
     * @return bool
     */
    public function sendConfirmationMessage(User $user, UserToken $userToken)
    {
        $subject = $this->getEmailConfirmationSubject();

        return $this->sendMessage(
            $user->email,
            $subject,
            'confirmation',
            ['user' => $user, 'userToken' => $userToken, 'subject' => $subject, 'module' => static::getUserModule()]
        );
    }

    /**
     * Email change confirmation message
     * From: /user/email/change
     *
     * @param User $user
     * @param UserToken $userToken
     * @return bool
     */
    public function sendEmailConfirmation(User $user, UserToken $userToken)
    {
        $email = $userToken->data;

        $subject = $this->getEmailConfirmationSubject();

        return $this->sendMessage(
            $email,
            $subject,
            'email_confirmation',
            ['user' => $user, 'userToken' => $userToken, 'subject' => $subject, 'module' => static::getUserModule()]
        );
    }

    /**
     * Send a new password to user
     * From: /user/password/forgot
     *
     * @param User $user
     * @param UserToken $userToken
     * @return bool
     */
    public function sendForgotPassword(User $user, UserToken $userToken)
    {
        $subject = $this->getForgotPasswordSubject();

        return $this->sendMessage(
            $user->email,
            $subject,
            'forgot_password',
            ['user' => $user, 'userToken' => $userToken, 'subject' => $subject, 'module' => static::getUserModule()]
        );
    }

    /**
     * Get welcome subject
     *
     * @return string
     */
    public function getWelcomeSubject()
    {
        if (is_null($this->welcomeSubject)) {
            $this->setWelcomeSubject(Yii::t('user', 'Welcome to {name}', ['name' => Yii::$app->name]));
        }

        return $this->welcomeSubject;
    }

    /**
     * Set welcome subject
     *
     * @param string $welcomeSubject
     */
    public function setWelcomeSubject($welcomeSubject)
    {
        $this->welcomeSubject = $welcomeSubject;
    }

    /**
     * @return string
     */
    public function getConfirmationSubject()
    {
        if (is_null($this->confirmationSubject)) {
            $this->setConfirmationSubject(Yii::t('user', 'Confirm account on {name}', ['name' => Yii::$app->name]));
        }

        return $this->confirmationSubject;
    }

    /**
     * @param string $confirmationSubject
     */
    public function setConfirmationSubject($confirmationSubject)
    {
        $this->confirmationSubject = $confirmationSubject;
    }

    /**
     * @return string
     */
    public function getEmailConfirmationSubject()
    {
        if (is_null($this->emailConfirmationSubject)) {
            $this->emailConfirmationSubject = Yii::t('user', 'Email confirmation on {name}', ['name' => Yii::$app->name]);
        }

        return $this->emailConfirmationSubject;
    }

    /**
     * @param string $emailConfirmationSubject
     */
    public function setEmailConfirmationSubject($emailConfirmationSubject)
    {
        $this->emailConfirmationSubject = $emailConfirmationSubject;
    }

    /**
     * @return string
     */
    public function getForgotPasswordSubject()
    {
        if (is_null($this->forgotPasswordSubject)) {
            $this->setForgotPasswordSubject(Yii::t('user', 'Your password has been changed on {name}', ['name' => Yii::$app->name]));
        }

        return $this->forgotPasswordSubject;
    }

    /**
     * @param string $forgotPasswordSubject
     */
    public function setForgotPasswordSubject($forgotPasswordSubject)
    {
        $this->forgotPasswordSubject = $forgotPasswordSubject;
    }

    /**
     * Send message
     *
     * @param string $to
     * @param string $subject
     * @param string $view
     * @param array $params
     * @return bool
     */
    protected function sendMessage($to, $subject, $view, $params= [])
    {
        $mailer = $this->getMailer();
        $mailer->viewPath = $this->viewPath;
        $mailer->getView()->theme = Yii::$app->view->theme;

        if (is_null($this->sender)) {
            $this->sender = isset(Yii::$app->params['adminEmail']) ? Yii::$app->params['adminEmail']: 'no-replay@example.com';
        }

        return $mailer->compose(['html' => 'html/' . $view, 'text' => 'text/' . $view], $params)
            ->setTo($to)
            ->setFrom($this->sender)
            ->setSubject($subject)
            ->send();
    }

    /**
     * Get mailer
     *
     * @return BaseMailer
     */
    public function getMailer()
    {
        if (! $this->mailer) {
            $this->setMailer(Yii::$app->mailer);
        }

        return $this->mailer;
    }

    /**
     * Set mailer
     *
     * @param $mailer
     */
    public function setMailer($mailer)
    {
        if (is_array($mailer) && isset($mailer['class'])) {
            $mailer = Yii::createObject($mailer);
        }

        $this->mailer = $mailer;
    }
}