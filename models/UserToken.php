<?php

namespace atans\user\models;

use atans\user\traits\UserModuleTrait;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * UserToken
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $token
 * @property string $data
 * @property string $created_at
 * @property string $expired_at
 *
 * @property User $user
 */
class UserToken extends ActiveRecord
{
    use UserModuleTrait;

    /* Email confirm for registration */
    const TYPE_CONFIRMATION = 'confirmation';

    /* Token for email change */
    const TYPE_EMAIL_CHANGE   = 'email_change';

    /* Token for forgot password */
    const TYPE_FORGOT_PASSWORD = 'forgot_password';

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('user', 'ID'),
            'user_id'    => Yii::t('user', 'User ID'),
            'type'       => Yii::t('user', 'Type'),
            'token'      => Yii::t('user', 'Token'),
            'data'       => Yii::t('user', 'Data'),
            'created_at' => Yii::t('user', 'Created At'),
            'expired_at' => Yii::t('user', 'Expired At'),
        ];
    }

    /**
     * Get token url
     *
     * @return string
     * @throws Exception
     */
    public function getUrl()
    {
        switch ($this->type) {
            case static::TYPE_CONFIRMATION:
                $route = '/user/register/confirm';
                break;
            case static::TYPE_EMAIL_CHANGE:
                $route = '/user/email/confirm';
                break;
            case static::TYPE_FORGOT_PASSWORD:
                $route = '/user/password/reset';
                break;
            default:
                throw new Exception('Invalid token type');
        }

        return Url::to([$route, 'token' => $this->token], true);
    }

    /**
     * Generate user token
     *
     * @param $userId
     * @param $type
     * @param null|string $data
     * @param null|string $expiredAt
     * @param null|string $token
     * @return array|UserToken|null|ActiveRecord|static
     * @throws \Exception
     */
    public static function generate($userId, $type, $data = null, $expiredAt = null, $token = null)
    {
        $transaction = Yii::$app->getDb()->beginTransaction();

        $userToken = static::findByUserId($userId, $type, false);

        if (! $userToken) {
            $userToken = new static;
        }

        try {
            $userToken->user_id    = $userId;
            $userToken->type       = $type;
            $userToken->token      = $token !== null ? $token : Yii::$app->security->generateRandomString();
            $userToken->data       = $data;
            $userToken->created_at = date('Y-m-d H:i:s');
            $userToken->expired_at = $expiredAt;

            if (! $userToken->save()) {
                throw new Exception('User token can not create or update.');
            }

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();

            Yii::warning($e->getMessage());

            throw $e;
        }

        return $userToken;
    }

    /**
     * Find a user token by userId
     *
     * @param int $userId
     * @param array|integer $type
     * @param boolean $checkExpiration
     * @return ActiveRecord|array|null|UserToken
     */
    public static function findByUserId($userId, $type, $checkExpiration = true)
    {
        return static::findUserToken(['user_id' => $userId,  'type' => $type], $checkExpiration);
    }

    /**
     * Find a user token by token
     *
     * @param string $token
     * @param array|integer $type
     * @param boolean $checkExpiration
     * @return ActiveRecord|array|null|UserToken
     */
    public static function findByToken($token, $type, $checkExpiration = true)
    {
        return static::findUserToken(['token' => $token,  'type' => $type], $checkExpiration);
    }

    /**
     * Find user token by condition
     *
     * @param array $condition
     * @param boolean $checkException
     * @return ActiveRecord|array|null|UserToken
     */
    public static function findUserToken(array $condition, $checkException)
    {
        $query = static::find()
            ->where($condition);
        
        if ($checkException) {
            $query->andWhere("expired_at >= :now OR expired_at IS NULL", [':now' => date('Y-m-d H:i:s')]);
        }

        return $query->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(self::getUserModule()->modelMap['User'], ['id' => 'user_id']);
    }
}