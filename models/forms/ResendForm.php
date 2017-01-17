<?php

namespace atans\user\models\forms;

use atans\user\Finder;
use atans\user\Mailer;
use Yii;
use atans\user\traits\UserModuleTrait;
use yii\base\Model;

class ResendForm extends Model
{
    use UserModuleTrait;

    public $email;

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var \atans\user\models\User
     */
    protected $user;

    /**
     * ResendForm constructor.
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
            'emailTrim'     => ['email', 'trim'],
            'emailPattern'  => ['email', 'email'],
            'emailExists'    => ['email', function($attribute) {
                $user = $this->getUser();
                if (! $user) {
                    $this->addError($attribute, Yii::t('user', 'Account {email} does not exist.', ['email' => $this->email]));
                    return;
                }

                if ($user->status !== static::getUserModule()->unconfirmedStatus) {
                    $this->addError($attribute, Yii::t('user', 'Your account is not required to confirm.'));
                }
            }]
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
    public function resend()
    {
        if (! $this->validate()) {
            return null;
        }

        $userModule = static::getUserModule();

        $user = $this->getUser();

        /* @var $userTokenModel \atans\user\models\UserToken */
        $userTokenModel = Yii::createObject([
            'class' => $userModule->modelMap['UserToken'],
        ]);

        $expiredAt = date('Y-m-d H:i:s', strtotime($userModule->confirmationExpireTime));

        $userToken = $userTokenModel::generate($user->id, $userTokenModel::TYPE_CONFIRMATION, null, $expiredAt);

        $this->mailer->sendConfirmationMessage($user, $userToken);

        return true;
    }

    /**
     * Get user by email
     *
     * @return \atans\user\models\User|null
     */
    protected function getUser()
    {
        if ($this->email) {
            $this->user = $this->finder->findUserByEmail($this->email);
        }

        return $this->user;
    }
}
