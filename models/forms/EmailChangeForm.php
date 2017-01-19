<?php

namespace atans\user\models\forms;

use atans\user\Finder;
use atans\user\Mailer;
use Yii;
use atans\user\models\User;
use atans\user\traits\UserModuleTrait;
use yii\base\Model;

class EmailChangeForm extends Model
{
    use UserModuleTrait;

    public $password;
    public $newEmail;
    public $newEmailConfirm;

    /* @var Finder */
    protected $finder;

    /* @var Mailer */
    protected $mailer;

    /*  @var User  */
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

            'newEmailRequired'         => ['newEmail', 'required'],
            'newEmailTrim'             => ['newEmail', 'trim'],
            'newEmailFilterStrtolower' => ['newEmail', 'filter', 'filter' => 'strtolower'],
            'newEmailPattern'          => ['newEmail', 'email'],
            'newEmailValidate'          => ['newEmail', function($attribute){
                $user = $this->getUser();
                if ($this->newEmail) {
                    if (! $user->isEmailChanged($this->newEmail)) {
                        $this->addError($attribute, Yii::t('user', 'Email does not changed.'));
                        return;
                    }

                    if ($this->getFinder()->findUserByEmail($this->newEmail)) {
                        $this->addError($attribute, Yii::t('user', 'Email {email} already taken.', ['email' => $this->newEmail]));
                        return;
                    }

                }
            }],

            'newEmailConfirmRequired' => ['newEmailConfirm', 'required'],
            'newEmailConfirmCompare'  => ['newEmailConfirm', 'compare', 'compareAttribute' => 'newEmail'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password'        => Yii::t('user', 'Current Password'),
            'newEmail'        => Yii::t('user', 'New Email'),
            'newEmailConfirm' => Yii::t('user', 'New Email Confirm'),
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

        $module = self::getUserModule();

        $user = $this->getUser();
        
        if ($module->enableEmailConfirmation) {
            /* @var $userTokenModel \atans\user\models\UserToken */
            $userTokenModel = Yii::createObject([
                'class' => $module->modelMap['UserToken'],
            ]);

            $expiredAt = date('Y-m-d H:i:s', strtotime($module->emailChangeConfirmationExpireTime));
            $userToken = $userTokenModel::generate($user->id, $userTokenModel::TYPE_EMAIL_CHANGE, $this->newEmail, $expiredAt);

            $this->getMailer()->sendEmailConfirmation($user, $userToken);
        } else {
            $user->changeEmail($this->newEmail);
        }

        return true;
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

    /**
     * Get finder
     *
     * @return Finder
     */
    protected function getFinder()
    {
        if (! $this->finder) {
            $this->finder = Yii::$container->get(Finder::className());
        }

        return $this->finder;
    }

    /**
     * Get mailer
     *
     * @return Mailer|object
     */
    protected function getMailer()
    {
        if (! $this->mailer) {
            $this->mailer = Yii::$container->get(Mailer::className());
        }

        return $this->mailer;
    }
}
