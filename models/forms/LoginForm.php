<?php
namespace atans\user\models\forms;

use atans\user\models\User;
use atans\user\Finder;
use atans\user\traits\UserModuleTrait;
use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    use UserModuleTrait;

    public $username;
    public $password;
    public $rememberMe;

    /**
     * @var \atans\user\models\User
     */
    protected $user;

    /**
     * @var \atans\user\Finder
     */
    protected $finder;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->rememberMe = self::getUserModule()->defaultRememberMe;

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'usernameRequired' => ['username', 'required'],

            'passwordRequired' => ['password', 'required'],
            'passwordValidate' => ['password', 'validatePassword'],

            'rememberMePattern' => ['rememberMe', 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username'   => Yii::t('user', 'Username'),
            'password'   => Yii::t('user', 'Password'),
            'rememberMe' => Yii::t('user', 'Remember Me'),
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
            $user = $this->getUser();

            $success = Yii::$app->user->login($user, $this->rememberMe ? self::getUserModule()->rememberMeDuration : 0);

            if ($success) {
                $user->updateLoginData();
            }

            return $success;
        }

        return false;
    }

    /**
     * Get user
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->user === null) {
            $this->user = $this->getFinder()->findUserByUsernameOrEmail($this->username);
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
}
