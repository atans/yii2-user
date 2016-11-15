<?php
namespace atans\user\models;

use atans\user\traits\ModuleTrait;
use Yii;
use yii\base\Model;
use common\models\User;
use yii\rbac\DbManager;

/**
 * Register form
 */
class RegisterForm extends Model
{
    use ModuleTrait;

    public $username;
    public $email;
    public $password;
    public $password_repeat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $module = $this->getModule();

        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => $module->modelMap['User'], 'message' => Yii::t('user', 'This username has already been taken.')],
            ['username', 'string', 'min' => $module->usernameMinLength, 'max' => $module->usernameMaxLength],
            ['username', 'match', 'pattern' => $module->usernamePattern],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => $module->modelMap['User'], 'message' => Yii::t('user', 'This email address has already been taken.')],

            ['password', 'required'],
            ['password', 'string', 'min' => $module->passwordMinLength],

            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('user', 'Username'),
            'email' => Yii::t('user', 'Email'),
            'password' => Yii::t('user', 'Password'),
            'password_repeat' => Yii::t('user', 'Password repeat'),
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

        $user           = new User();
        $user->username = $this->username;
        $user->email    = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        $user->status = $this->getModule()->defaultStatus;

        $user->save();

         return $user;
    }
}
