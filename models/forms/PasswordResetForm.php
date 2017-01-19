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
    public $newPasswordConfirm;

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

            'newPasswordConfirmRequired' => ['newPasswordConfirm', 'required'],
            'newPasswordConfirmCompare' => ['newPasswordConfirm', 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'newPassword'       => Yii::t('user', 'New Password'),
            'newPasswordConfirm' => Yii::t('user', 'New Password Confirm'),
        ];
    }
}
