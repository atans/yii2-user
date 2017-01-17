<?php

namespace atans\user\models;

use atans\user\Mailer;
use atans\user\traits\UserModuleTrait;
use atans\user\traits\StatusTrait;
use Yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\Application as WebApplication;
use yii\web\NotAcceptableHttpException;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property string $registration_ip
 * @property string $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 *
 * @property-read Mailer $mailer
 */
class User extends ActiveRecord implements IdentityInterface
{
    use UserModuleTrait;
    use StatusTrait;

    const SCENARIO_REGISTER        = 'register';
    const SCENARIO_CREATE          = 'create';
    const SCENARIO_UPDATE          = 'update';
    const SCENARIO_CHANGE_EMAIL    = 'change_email';

    const STATUS_UNCONFIRMED       = 'unconfirmed';
    const STATUS_INACTIVE          = 'inactive';
    const STATUS_ACTIVE            = 'active';
    const STATUS_DELETED           = 'deleted';
    const STATUS_UNCONFIRMED_EMAIL = 'unconfirmed_email';
    const STATUS_PENDING           = 'pending';
    const STATUS_BLOCKED           = 'blocked';

    const EVENT_BEFORE_CREATE = 'before_create';
    const EVENT_AFTER_CREATE  = 'before_create';

    const EVENT_BEFORE_REGISTER = 'before_register';
    const EVENT_AFTER_REGISTER  = 'after_register';

    private $password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'      => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value'      => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $module = static::getUserModule();

        return [
            'usernameRequired' => ['username', 'required'],
            'usernameTrim'     => ['username', 'trim'],
            'usernamePattern'  => ['username', 'match', 'pattern' => $module->usernamePattern],
            'usernameUnique'   => ['username', 'unique', 'message' => Yii::t('user', 'This username has already been taken')],
            'usernameLength'   => ['username', 'string', 'min' => $module->usernameMinLength, 'max' => $module->usernameMaxLength],

            'emailRequired'    => ['email', 'required'],
            'emailTrim'        => ['email', 'trim'],
            'emailFilter'      => ['email', 'filter', 'filter' => 'strtolower'],
            'emailPattern'     => ['email', 'email'],
            'emailLength'      => ['email', 'string', 'max' => $module->emailMaxLength],
            'emailUnique'      => ['email', 'unique', 'message' => Yii::t('user', 'The email has been already used')],

            'passwordRequired' => ['password', 'required', 'on' => [static::SCENARIO_REGISTER, static::SCENARIO_CREATE]],
            'passwordLength'   => ['password', 'string', 'min' => $module->passwordMinLength],

            'statusRequired'   => ['status', 'required', 'on' => [static::SCENARIO_CREATE, static::SCENARIO_UPDATE]],
            'statusFilter'     => ['status', 'filter', 'filter' => 'strtolower'],
            'statusRange'      => ['status', 'in', 'range' => static::getStatuses()],

            'createdAtPattern' => ['created_at', 'date', 'format' => 'php:Y-m-d H:i:s'],
            'updatedAtPattern' => ['updated_at', 'date', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[static::SCENARIO_REGISTER] = ['username', 'email', 'password'];
        $scenarios[static::SCENARIO_CREATE]   = ['username', 'email', 'password', 'status'];
        $scenarios[static::SCENARIO_UPDATE]   = ['username', 'email', 'password', 'status'];
        $scenarios[static::SCENARIO_CHANGE_EMAIL]   = ['email'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username'        => Yii::t('user', 'Username'),
            'email'           => Yii::t('user', 'Email'),
            'registration_ip' => Yii::t('user', 'Registration IP'),
            'status'          => Yii::t('user', 'Status'),
            'created_at'      => Yii::t('user', 'Created At'),
            'Updated_at'      => Yii::t('user', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getStatusItems()
    {
        return [
            static::STATUS_PENDING => Yii::t('user', 'Pending'),
            static::STATUS_ACTIVE  => Yii::t('user', 'Active'),
            static::STATUS_BLOCKED => Yii::t('user', 'Blocked'),
        ];
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsernameOrEmail($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (! static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status'               => static::STATUS_ACTIVE,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password      = $password;
        $this->password_hash = Yii::$app->security->generatePasswordHash($password, static::getUserModule()->passwordCost);
    }

    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Activate account
     *
     * @return bool
     */
    public function activate()
    {
        if (! $this->status == static::STATUS_PENDING) {
            return false;
        }

        return (bool)$this->updateAttributes(['status' => static::STATUS_ACTIVE]);
    }

    /**
     * Block account
     *
     * @return bool
     */
    public function block()
    {
        return (bool)$this->updateAttributes(['status' => static::STATUS_BLOCKED]);
    }

    /**
     * Unblock account
     *
     * @return bool
     */
    public function unblock()
    {
        return (bool)$this->updateAttributes(['status' => static::STATUS_ACTIVE]);
    }

    public function getIsBlocked()
    {
        return $this->status == static::STATUS_BLOCKED;
    }

    /**
     * Check user is admin or not
     *
     * @return bool
     */
    public function getIsAdmin()
    {
        if (Yii::$app->getAuthManager()
            && static::getUserModule()->adminPermission
            && Yii::$app->user->can(static::getUserModule()->adminPermission)
        ) {
            return true;
        }

        return in_array($this->username, static::getUserModule()->admins);
    }

    /**
     * Create user
     *
     * @return bool
     * @throws NotAcceptableHttpException
     * @throws \yii\db\Exception
     * @throws \Exception
     */
    public function create()
    {
        if (! $this->getIsNewRecord()) {
            throw new NotAcceptableHttpException(sprintf('%s: Existing user can not create', __METHOD__));
        }

        $transaction = $this->getDb()->beginTransaction();

        try {
            if (is_null($this->password)) {
                $this->password = Yii::$app->security->generateRandomString(8);
            }

            $this->trigger(static::EVENT_BEFORE_CREATE);

            if (! $this->save()) {
                throw new Exception('User can not create');
            }

            $transaction->commit();

            $this->trigger(static::EVENT_AFTER_CREATE);

        } catch (\Exception $e) {
            $transaction->rollBack();

            Yii::warning($e->getMessage());
            throw $e;
        }

        return true;
    }

    /**
     * Register user
     *
     * @return bool
     * @throws NotAcceptableHttpException
     * @throws \yii\db\Exception
     * @throws \Exception
     */
    public function register()
    {
        if (! $this->getIsNewRecord()) {
            throw new NotAcceptableHttpException(sprintf('%s: Existing user can not register', __METHOD__));
        }

        $transaction = $this->getDb()->beginTransaction();
        $module = static::getUserModule();

        try {
            $this->status = $module->defaultStatus;

            $userToken = null;
            if ($module->enableConfirmation) {
                $this->status = $module->unconfirmedStatus;
            }

            if (static::getUserModule()->enableGeneratingPassword && is_null($this->password)) {
                $this->setPassword(Yii::$app->security->generateRandomString(8));
            }

            $this->trigger(static::EVENT_BEFORE_REGISTER);

            if (! $this->save()) {
                throw new Exception('User can not register');
            }

            if ($module->enableConfirmation) {
                /* @var $userTokenModel \atans\user\models\UserToken */
                $userTokenModel = Yii::createObject([
                    'class' => $module->modelMap['UserToken'],
                ]);

                $expired_at = date('Y-m-d H:i:s', strtotime($module->confirmationExpireTime));

                $userToken = $userTokenModel::generate($this->id, $userTokenModel::TYPE_CONFIRMATION, null, $expired_at);
            }

            // Send welcome message
            $this->mailer->sendWelcomeMessage($this, $userToken);

            $this->trigger(static::EVENT_AFTER_REGISTER);

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::warning($e->getMessage());
            throw $e;
        }

        return true;
    }

    /**
     * Confirm account
     *
     * @return bool
     * @throws \Exception
     */
    public function confirm()
    {
        $transaction = Yii::$app->getDb()->beginTransaction();
        $userModule = static::getUserModule();

        try {
            // Incorrect unconfirmed status
            if ($this->status !== $userModule->unconfirmedStatus) {
                return false;
            }

            // Account is confirmed
            if ($this->status == $userModule->confirmedStatus) {
                return true;
            }

            $this->status = static::getUserModule()->confirmedStatus;

            if (! $this->save(false, ['status'])) {
                throw new Exception('User can not confirm');
            }

            $transaction->commit();

            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();

            Yii::warning($e->getMessage());
            throw $e;
        }
    }

    public function changeEmail($email)
    {
        $transaction = $this->getDb()->beginTransaction();

        try {
            $this->email = $email;

            if (! $this->save()) {
                throw new Exception('User can not change email');
            }

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::warning($e->getMessage());
            throw $e;
        }

        return true;
    }

    public function isEmailChanged($newEmail)
    {
        return $this->email !== $newEmail;
    }

    /**
     * Change password
     *
     * @param $password
     * @return bool
     * @throws \Exception
     */
    public function changePassword($password)
    {
        $transaction = $this->getDb()->beginTransaction();

        try {
            $this->setPassword($password);
            $this->generateAuthKey();

            if (! $this->save(false, ['password_hash', 'auth_key'])) {
                throw new Exception('User can not change password');
            }

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::warning($e->getMessage());
            throw $e;
        }

        return true;
    }

    /**
     * send new password to user
     *
     * @return bool
     */
    public function resendPassword()
    {
        $password = Yii::$app->security->generateRandomString(8);
        $this->setPassword($password);
        $this->save(false, ['password_hash']);

        return $this->getMailer()->sendGeneratedPassword($this, $password);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->generateAuthKey();
            if (Yii::$app instanceof WebApplication) {
                $this->setAttribute('registration_ip', Yii::$app->request->userIP);
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * Get mailer
     *
     * @return Mailer|object
     */
    protected function getMailer()
    {
        return Yii::$container->get(Mailer::className());
    }
}
