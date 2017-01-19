<?php

namespace atans\user;

use atans\user\models\User;
use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    /* @var bool */
    public $enableRegistration = true;

    /*  @var string default go home */
    public $registerRedirect;

    /*  @var string */
    public $usernamePattern = '/^[a-z0-9_\-\.]+$/i';

    /* @var int */
    public $usernameMinLength = 3;

    /* @var int */
    public $usernameMaxLength = 255;

    /*  @var int */
    public $emailMaxLength = 255;

    /* @var int */
    public $passwordMinLength = 6;

    /*  @var bool  */
    public $enableGeneratingPassword = false;

    /* @var int @see http://php.net/manual/en/function.password-hash.php  */
    public $passwordCost = null;

    /* @var string  Custom user default after register */
    public $defaultStatus = User::STATUS_ACTIVE;

    /* @var boolean After register user default status is Module->unconfirmedStatus if enable */
    public $enableConfirmation = false;

    /* @var string Confirmation token expire time, eg: "+ 30 minutes" */
    public $confirmationExpireTime = "+ 30 minutes";

    /* @var string */
    public $unconfirmedStatus = User::STATUS_UNCONFIRMED;

    /*  @var string  Email confirmed status []*/
    public $confirmedStatus = User::STATUS_ACTIVE;

    public $enableEmailChange = true;

    /* @var string email change confirmation token expire time, eg: "+ 30 minutes" */
    public $emailChangeConfirmationExpireTime = "+ 30 minutes";

    /* @var bool */
    public $defaultRememberMe = true;

    /* @var int  */
    public $rememberMeDuration = 2592000;

    /* @var array  */
    public $modelMap = [];

    /* @var bool  */
    public $enableAutoLogin = true;

    /* @var array Login url */
    public $loginUrl = ['/user/login'];

    /* @var array Login redirect */
    public $loginRedirect = null;

    /* @var array Logout redirect */
    public $logoutRedirect = null;

    /* @var int */
    public $passwordResetTokenExpire = 3600;

    /* @var int Forgot email token expired time */
    public $forgotEmailExpireTime = '+30 minutes';

    /*  @var array Mailer options */
    public $mailer = [];

    /* @var array Admin roles for /user/admin */
    public $adminRoles = ['admin'];

    /* @var array */
    public $admins = [];

    /* @var array */
    public $adminPermission = [];
}