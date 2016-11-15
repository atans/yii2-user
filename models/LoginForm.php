<?php
namespace atans\user\models;

use atans\user\Finder;
use atans\user\traits\ModuleTrait;
use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    use ModuleTrait;

    public $identity;
    public $password;
    public $rememberMe;

    /**
     * @var \atans\user\models\User
     */
    private $user;

    /**
     * @var \atans\user\Finder
     */
    private $finder;

    /**
     * Constructor
     *
     * @param Finder $finder
     * @param array $config
     */
    public function __construct(Finder $finder, $config = [])
    {
        $this->finder     = $finder;
        $this->rememberMe = $this->getModule()->defaultRememberMe;

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['identity', 'required'],
            ['password', 'required'],

            ['password', 'validatePassword'],

            ['rememberMe', 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'identity'   => Yii::t('user', 'Username'),
            'password'   => Yii::t('user', 'Password'),
            'rememberMe' => Yii::t('user', 'Remember me'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params     the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (! $this->hasErrors()) {
            $user = $this->getUser();
            if (! $user || ! $user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('user', 'Incorrect username or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? $this->getModule()->rememberMeDuration : 0);
        } else {
            return false;
        }
    }

    /**
     * Get user
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->user === null) {
            $this->user = $this->finder->findUserByUsernameOrEmail($this->identity);
        }

        return $this->user;
    }
}
