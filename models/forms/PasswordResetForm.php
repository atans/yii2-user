<?php

namespace atans\user\models\forms;

use Yii;
use atans\user\models\User;
use atans\user\traits\UserModuleTrait;
use yii\base\Model;

class PasswordResetForm extends Model
{
    use UserModuleTrait;

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
            'newPassword'       => Yii::t('user', 'New Password'),
            'newPasswordRepeat' => Yii::t('user', 'New Password repeat'),
        ];
    }
}
