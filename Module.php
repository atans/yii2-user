<?php

namespace atans\user;

use Yii;
use yii\base\Module as BaseModule;
use yii\filters\AccessControl;

class Module extends BaseModule
{
    /**
     * @var bool
     */
    public $enableRegistration = true;

    /**
     * @var string default @web
     */
    public $redirectUrlAfterRegistration;

    /**
     * @var string
     */
    public $usernamePattern = '/^[a-z0-9_\-\.]+$/i';

    /**
     * @var int
     */
    public $usernameMinLength = 3;

    /**
     * @var int
     */
    public $usernameMaxLength = 255;

    /**
     * @var int
     */
    public $emailMaxLength = 255;

    /**
     * @var int
     */
    public $passwordMinLength = 6;

    /**
     * @var bool
     */
    public $enableGeneratingPassword = false;

    /**
     * @see http://php.net/manual/en/function.password-hash.php
     * @var int
     */
    public $passwordCost = null;

    /**
     * @var string
     */
    public $defaultStatus = models\User::STATUS_ACTIVE;

    /**
     * @var bool
     */
    public $defaultRememberMe = true;

    /**
     * @var int
     */
    public $rememberMeDuration = 2592000;

    /**
     * @var array
     */
    public $modelMap = [];

    /**
     * @var bool
     */
    public $enableAutoLogin = true;

    /**
     * @var array Login url
     */
    public $loginUrl = ['/user/login'];

    /**
     * @var array
     */
    public $urlRules = [
        [
            'class' => 'yii\web\GroupUrlRule',
            'prefix' => 'user',
            'rules' => [
                '<action:(login|register|logout)>'    => 'default/<action>',
            ],
        ],
    ];

    /**
     * @var array
     */
    public $admins = [];

    /**
     * @var array
     */
    public $adminPermission = [];

}