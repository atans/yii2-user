<?php
namespace atans\user\models;

use atans\user\traits\ModuleTrait;
use Yii;
use yii\base\Model;

class RegistrationForm extends Model
{
    use ModuleTrait;

    public $username;
    public $email;
    public $password;
    public $passwordRepeat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $module = $this->getModule();

        return [
            ['username', 'required'],
            ['username', 'trim'],
            ['username', 'filter', 'filter' => 'strtolower'],
            ['username', 'unique', 'targetClass' => $module->modelMap['User'], 'message' => Yii::t('user', 'This username has already been taken.')],
            ['username', 'string', 'min' => $module->usernameMinLength, 'max' => $module->usernameMaxLength],
            ['username', 'match', 'pattern' => $module->usernamePattern],

            ['email', 'required'],
            ['email', 'trim'],
            ['email', 'filter', 'filter'=>'strtolower'],
            ['email', 'email'],
            ['email', 'string', 'max' => $module->emailMaxLength],
            ['email', 'unique', 'targetClass' => $module->modelMap['User'], 'message' => Yii::t('user', 'This email address has already been taken.')],

            ['password', 'required'],
            ['password', 'string', 'min' => $module->passwordMinLength],

            ['passwordRepeat', 'compare', 'compareAttribute' => 'password'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username'       => Yii::t('user', 'Username'),
            'email'          => Yii::t('user', 'Email'),
            'password'       => Yii::t('user', 'Password'),
            'passwordRepeat' => Yii::t('user', 'Password Repeat'),
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function register()
    {
        if (! $this->validate()) {
            return null;
        }

        /* @var $user User */
        $user = Yii::createObject([
            'class' => $this->getModule()->modelMap['User'],
            'scenario' => User::SCENARIO_REGISTER,
        ]);
        $user->setAttributes($this->getAttributes());

        if (! $user->register()) {
            return false;
        }

        return true;
    }
}
