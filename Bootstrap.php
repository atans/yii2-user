<?php

namespace atans\user;

use Yii;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yii\i18n\PhpMessageSource;

class Bootstrap implements BootstrapInterface
{
    private $modelMap = [
        'User'             => 'atans\user\models\User',
        'LoginForm'        => 'atans\user\models\LoginForm',
        'RegistrationForm' => 'atans\user\models\RegistrationForm',
        'UserSearch'       => 'atans\user\models\UserSearch',
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
                'userQuery' =>  Yii::$container->get('UserQuery'),
            ]);
        }

        if ($app instanceof ConsoleApplication) {

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

            $urlRules = [];
            foreach ($module->urlRules as $urlRule) {
                $urlRules[] = Yii::createObject($urlRule);
            }

            $app->urlManager->addRules($urlRules, false);
        }
    }
}
