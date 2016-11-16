<?php

namespace atans\user\models;

use atans\user\traits\ModuleTrait;
use atans\user\traits\StatusTrait;
use Yii;
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
 */
class User extends ActiveRecord implements IdentityInterface
{
    use ModuleTrait;
    use StatusTrait;

    const SCENARIO_REGISTER = 'register';
    const SCENARIO_CREATE   = 'create';
    const SCENARIO_UPDATE   = 'update';

    const STATUS_ACTIVE  = 'active';
    const STATUS_DELETED = 'deleted';
    const STATUS_PENDING = 'pending';
    const STATUS_BLOCKED = 'blocked';


    const BEFORE_CREATE = 'before_create';
    const AFTER_CREATE = 'before_create';

    const BEFORE_REGISTER = 'before_register';
    const AFTER_REGISTER = 'after_register';

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
        $module = $this->getModule();

        return [
            ['username', 'required'],
            ['username', 'trim'],
            ['username', 'filter', 'filter'=>'strtolower'],
            ['username', 'match', 'pattern' => $module->usernamePattern],
            ['username', 'unique'],
            ['username', 'string', 'min' => $module->usernameMinLength, 'max' => $module->usernameMaxLength],

            ['email', 'required'],
            ['email', 'trim'],
            ['email', 'filter', 'filter'=>'strtolower'],
            ['email', 'email'],
            ['email', 'string', 'max' => $module->emailMaxLength],
            ['email', 'unique', 'message' => Yii::t('user', 'The email has been already used')],

            ['password', 'required'],
            ['password', 'string', 'min' => $module->passwordMinLength],

            ['status', 'filter', 'filter'=>'strtolower'],
            ['status', 'in', 'range' => self::getStatuses()],

            ['created_at', 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['updated_at', 'date', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_REGISTER] = ['username', 'email', 'password'];
        $scenarios[self::SCENARIO_CREATE] = ['username', 'email', 'password', 'status'];
        $scenarios[self::SCENARIO_UPDATE] = ['username', 'email', 'password', 'status'];


        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username'   => Yii::t('user', 'Username'),
            'email'      => Yii::t('user', 'Email'),
            'registration_ip'      => Yii::t('user', 'Registration IP'),
            'status'     => Yii::t('user', 'Status'),
            'created_at' => Yii::t('user', 'Created At'),
            'Updated_at' => Yii::t('user', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getStatusValueOptions()
    {
        return [
            self::STATUS_PENDING => Yii::t('user', 'Pending'),
            self::STATUS_ACTIVE => Yii::t('user', 'Active'),
            self::STATUS_BLOCKED => Yii::t('user', 'Blocked'),
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
            'status'               => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire    = Yii::$app->params['user.passwordResetTokenExpire'];

        return $timestamp + $expire >= time();
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
        $this->password_hash = Yii::$app->security->generatePasswordHash($password, $this->getModule()->passwordCost);
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

    public function block()
    {
        return (bool) $this->updateAttributes(['status' => self::STATUS_BLOCKED]);
    }

    /**
     * Create user
     *
     * @return bool
     * @throws NotAcceptableHttpException
     * @throws \yii\db\Exception
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

            $this->trigger(self::BEFORE_CREATE);

            if (! $this->save()) {
                $transaction->rollBack();
            }

            $transaction->commit();

            $this->trigger(self::AFTER_CREATE);

            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            return true;
        }
    }

    /**
     * Register user
     *
     * @return bool
     * @throws NotAcceptableHttpException
     * @throws \yii\db\Exception
     */
    public function register()
    {
        if (! $this->getIsNewRecord()) {
            throw new NotAcceptableHttpException(sprintf('%s: Existing user can not register', __METHOD__));
        }

        $transaction = $this->getDb()->beginTransaction();

        try {
            $this->status = $this->getModule()->defaultStatus;

            if ($this->getModule()->enableGeneratingPassword && is_null($this->password)) {
                $this->password = Yii::$app->security->generateRandomString(8);
            }

            $this->trigger(self::BEFORE_REGISTER);

            if (! $this->save()) {
                $transaction->rollBack();
            }

            $transaction->commit();

            $this->trigger(self::AFTER_REGISTER);

            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            return true;
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            if (Yii::$app instanceof WebApplication) {
                $this->setAttribute('registration_ip', Yii::$app->request->userIP);
            }
        }

        return parent::beforeSave($insert);
    }

}
