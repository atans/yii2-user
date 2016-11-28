<?php

namespace atans\user\models;

use atans\user\models\User;
use Yii;
use atans\user\traits\ModuleTrait;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    use ModuleTrait;

    public $password;
    public $newPassword;
    public $newPasswordRepeat;

    /**
     * @var User
     */
    private $user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'trim'],
            ['password', function($attribute){
                if (! $this->getUser()->validatePassword($this->password)) {
                    $this->addError($attribute, Yii::t('user', 'Incorrect current password.'));
                }
            }],

            ['newPassword', 'required'],
            ['newPassword', 'trim'],
            ['newPassword', 'string', 'min' => $this->getModule()->passwordMinLength],

            ['newPasswordRepeat', 'required'],
            ['newPasswordRepeat', 'compare', 'compareAttribute' => 'newPassword'],
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
        $user->setPassword($this->newPassword);

        return $user->save();
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
