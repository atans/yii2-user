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
            'usernameRequired' => ['username', 'required'],
            'usernameTrim'     => ['username', 'trim'],
            'usernamePattern'  => ['username', 'match', 'pattern' => $module->usernamePattern],
            'usernameUnique'   => ['username', 'unique', 'targetClass' => $module->modelMap['User'], 'message' => Yii::t('user', 'This username has already been taken')],
            'usernameLength'   => ['username', 'string', 'min' => $module->usernameMinLength, 'max' => $module->usernameMaxLength],

            'emailRequired'    => ['email', 'required'],
            'emailTrim'        => ['email', 'trim'],
            'emailFilter'      => ['email', 'filter', 'filter' => 'strtolower'],
            'emailPattern'     => ['email', 'email'],
            'emailLength'      => ['email', 'string', 'max' => $module->emailMaxLength],
            'emailUnique'      => ['email', 'unique',  'targetClass' => $module->modelMap['User'],'message' => Yii::t('user', 'The email has been already used')],

            'passwordRequired' => ['password', 'required'],
            'passwordLength' => ['password', 'string', 'min' => $module->passwordMinLength],

            'passwordRepeatRequired' => ['password', 'required'],
            'passwordRepeatCompare' => ['passwordRepeat', 'compare', 'compareAttribute' => 'password'],
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
     * @return boolean|null the saved model or null if saving fails
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
