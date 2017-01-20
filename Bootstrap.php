<?php

namespace atans\user;

use Yii;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yii\i18n\PhpMessageSource;

class Bootstrap implements BootstrapInterface
{
    /**
     * @var array model map
     */
    private $modelMap = [
        'User'               => 'atans\user\models\User',
        'UserToken'          => 'atans\user\models\UserToken',
        'LoginForm'          => 'atans\user\models\forms\LoginForm',
        'RegisterForm'       => 'atans\user\models\forms\RegisterForm',
        'ResendForm'         => 'atans\user\models\forms\ResendForm',
        'EmailChangeForm'    => 'atans\user\models\forms\EmailChangeForm',
        'PasswordChangeForm' => 'atans\user\models\forms\PasswordChangeForm',
        'PasswordForgotForm' => 'atans\user\models\forms\PasswordForgotForm',
        'PasswordResetForm'  => 'atans\user\models\forms\PasswordResetForm',
        'UserSearch'         => 'atans\user\models\search\UserSearch',
    ];

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        /* @var $module Module */
        /* @var $modelName \yii\db\ActiveRecord */
        if ($app->hasModule('user') && ($module = $app->getModule('user')) instanceof Module) {
            $this->modelMap = array_merge($this->modelMap, $module->modelMap);

            foreach ($this->modelMap as $name => $definition) {
                $class = 'atans\\user\\model\\' . $name;

                Yii::$container->set($class, $definition);
                $modelName               = is_array($definition) ? $definition['class'] : $definition;
                $module->modelMap[$name] = $modelName;

                if (in_array($name, ['User'])) {
                    Yii::$container->set($name . 'Query', function () use ($modelName) {
                        return $modelName::find();
                    });
                }
            }

            Yii::$container->setSingleton(Finder::className(), [
                'userQuery' => Yii::$container->get('UserQuery'),
            ]);
        }

        if ($app instanceof ConsoleApplication) {
            // TODO
        } else {
            if (! isset($app->i18n->translations['user*'])) {
                $app->i18n->translations['user*'] = [
                    'class'    => PhpMessageSource::className(),
                    'basePath' => __DIR__ . '/messages',
                ];
            }

            Yii::$container->set('yii\web\User', [
                'enableAutoLogin' => $module->enableAutoLogin,
                'loginUrl'        => $module->loginUrl,
                'identityClass'   => $module->modelMap['User'],
            ]);

            Yii::$container->set(Mailer::className(), $module->mailer);
        }
    }
}
