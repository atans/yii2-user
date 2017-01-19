<?php

namespace atans\user\models\forms;

use atans\user\Finder;
use atans\user\Mailer;
use Yii;
use atans\user\models\User;
use atans\user\traits\UserModuleTrait;
use yii\base\Model;

class PasswordForgotForm extends Model
{
    use UserModuleTrait;

    public $email;

    /**
     * @var finder
     */
    protected $finder;

    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var User
     */
    protected $user;

    /**
     * PasswordForgotForm constructor.
     *
     * @param Finder $finder
     * @param Mailer $mailer
     * @param array $config
     */
    public function __construct(Finder $finder, Mailer $mailer, array $config = [])
    {
        $this->finder = $finder;
        $this->mailer = $mailer;
        
        parent::__construct($config);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'emailRequired' => ['email', 'required'],
            'emailTrim' => ['email', 'trim'],
            'emailFilterStrtolower' => ['email', 'filter', 'filter' => 'strtolower'],
            'emailPattern' => ['email', 'email'],
            'emailExist' => ['email', function($attribute){
                $user = $this->getUser();
                if (! $user) {
                    $this->addError($attribute, Yii::t('user', "Email does not found."));
                }
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('user', 'Email'),
        ];
    }

    /**
     * Change password
     *
     * @return bool|null
     */
    public function sendForgotEmail()
    {
        if (! $this->validate()) {
            return null;
        }

        /* @var $userTokenModel \atans\user\models\UserToken */
        $userTokenModel = Yii::createObject([
            'class' => self::getUserModule()->modelMap['UserToken'],
        ]);

        $user = $this->getUser();

        $expired_at = date('Y-m-d H:i:s', strtotime(self::getUserModule()->forgotEmailExpireTime));

        $userToken = $userTokenModel::generate($user->id, $userTokenModel::TYPE_FORGOT_PASSWORD, null, $expired_at);

        return $this->mailer->sendForgotPassword($user, $userToken);
    }

    /**
     * @return User|null|\yii\web\IdentityInterface
     */
    protected function getUser()
    {
        if (! $this->user) {
            $this->user = $this->finder->findUserByEmail($this->email);
        }

        return $this->user;
    }
}
