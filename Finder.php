<?php

namespace atans\user;

use yii\db\ActiveQuery;
use yii\base\Object;
use yii\validators\EmailValidator;

class Finder extends Object
{
    /**
     * @var ActiveQuery
     */
    protected $userQuery;

    /**
     * Find user by username or email
     *
     * @param string $usernameOrEmail
     * @return models\User|null
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if ((new EmailValidator)->validate($usernameOrEmail)) {
            return $this->findUserByEmail($usernameOrEmail);
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    /**
     * Find user by username
     *
     * @param string $username
     * @return models\User|null
     */
    public function findUserByUsername($username)
    {
        return $this->findUser(['username' => $username])->one();
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @return models\User|null
     */
    public function findUserByEmail($email)
    {
        return $this->findUser(['email' => $email])->one();
    }

    /**
     * @param array $condition
     * @return \yii\db\ActiveQuery
     */
    public function findUser(array $condition)
    {
        return $this->getUserQuery()->where($condition);
    }

    /**
     * Get userQuery
     *
     * @return ActiveQuery
     */
    public function getUserQuery()
    {
        return $this->userQuery;
    }

    /**
     * Set userQuery
     *
     * @param  ActiveQuery $userQuery
     * @return Finder
     */
    public function setUserQuery(ActiveQuery $userQuery)
    {
        $this->userQuery = $userQuery;
        return $this;
    }
}
