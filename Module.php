<?php

namespace atans\user;

use atans\user\models\User;

class Module extends \yii\base\Module
{

    public $enableRegistration = true;

    /**
     * @var string
     */
    public $usernamePattern = '/^[a-z0-9_-\.]{3,15}$/';

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
    public $defaultStatus = User::STATUS_ACTIVE;

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
    public $admins = [];

    /**
     * @var array
     */
    public $adminPermission = [];


    /**
     * @var array
     */
    public $modelMap = [];

}