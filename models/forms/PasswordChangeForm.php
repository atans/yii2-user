<?php

namespace atans\user\models\forms;

use Yii;
use atans\user\models\User;
use atans\user\traits\UserModuleTrait;
use yii\base\Model;

class PasswordChangeForm extends Model
{
    use UserModuleTrait;

    public $password;
    public $newPassword;
    public $newPasswordRepeat;

    /**
     * @var User
     */
    protected $user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'passwordRequired' => ['password', 'required'],
            'passwordTrim' => ['password', 'trim'],
            'passwordValidate' => ['password', function($attribute){
                if (! $this->getUser()->validatePassword($this->password)) {
                    $this->addError($attribute, Yii::t('user', 'Incorrect current password.'));
                }
            }],

            'newPasswordRequired' => ['newPassword', 'required'],
            'newPasswordTrim' => ['newPassword', 'trim'],
            'newPasswordString' => ['newPassword', 'string', 'min' => self::getUserModule()->passwordMinLength],

            'newPasswordRepeatRequired' => ['newPasswordRepeat', 'required'],
            'newPasswordRepeatCompare' => ['newPasswordRepeat', 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password'          => Yii::t('user', 'Current password'),
            'newPassword'       => Yii::t('user', 'New Password'),
            'newPasswordRepeat' => Yii::t('user', 'New Password repeat'),
        ];
    }

    /**
     * Change password
     *
     * @return bool|null
     */
    public function change()
    {
        if (! $this->validate()) {
            return null;
        }

        $user = $this->getUser();
        $success = $user->changePassword($this->newPassword);

        return $success;
    }

    /**
     * @return User|null|\yii\web\IdentityInterface
     */
    protected function getUser()
    {
        if (! $this->user) {
            $this->user = Yii::$app->user->getIdentity();
        }

        return $this->user;
    }
}
