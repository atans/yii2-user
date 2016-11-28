<?php

namespace atans\user\controllers;

use atans\user\models\User;
use atans\user\traits\AjaxValidationTrait;
use Yii;
use atans\user\Finder;
use atans\user\traits\ModuleTrait;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class AdminController extends Controller
{
    use AjaxValidationTrait;
    use ModuleTrait;

    const URL_REMEMBER = 'user-admin';

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete'  => ['post'],
                    'block'   => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param string $id
     * @param \yii\base\Module $module
     * @param Finder $finder
     * @param array $config
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        Url::remember('', self::URL_REMEMBER);

        $searchModel = Yii::createObject($this->getModule()->modelMap['UserSearch']);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        /* @var $model User*/
        $model = Yii::createObject([
            'class' => $this->getModule()->modelMap["User"],
            'scenario' => User::SCENARIO_CREATE,
        ]);

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->create()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been created'));

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        Url::remember('', self::URL_REMEMBER);

        $model = $this->findModel($id);
        $model->setScenario(User::SCENARIO_UPDATE);

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been updated'));

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        if (Yii::$app->user->getId() == $id) {
            Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'You can not delete own account.'));

            return $this->redirect(['index']);
        }

        $model = $this->findModel($id);

        if ($model->delete()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been deleted.'));
        } else {
            Yii::$app->getSession()->setFlash('warning', Yii::t('user', 'User can not delete.'));
        }

        return $this->redirect(['index']);
    }

    public function actionAssignments($id)
    {
        if (! isset(Yii::$app->extensions['atans/yii2-rbac'])) {
            throw new NotFoundHttpException();
        }

        Url::remember('', self::URL_REMEMBER);

        $model = $this->findModel($id);

        return $this->render('assignments', [
            'model' => $model,
        ]);
    }

    public function actionActivate($id)
    {
        if (Yii::$app->user->getId() == $id) {
            Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'You can not activate own account.'));

            return $this->redirect(['index']);
        }

        $model = $this->findModel($id);
        if ($model->activate()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been Activated.'));
        } else {
            Yii::$app->getSession()->setFlash('warning', Yii::t('user', 'User can not activate.'));
        }

        return $this->redirect(Url::previous(self::URL_REMEMBER));
    }

    public function actionBlock($id)
    {
        if (Yii::$app->user->getId() == $id) {
            Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'You can not block own account.'));

            return $this->redirect(['index']);
        }

        $model = $this->findModel($id);
        if ($model->status == User::STATUS_BLOCKED) {
            $model->unblock();
        } else {
            $model->block();
        }

        return $this->redirect(Url::previous(self::URL_REMEMBER));
    }

    public function actionView($id)
    {
        Url::remember('', self::URL_REMEMBER);

        $model = $this->findModel($id);

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Find model
     *
     * @param $id
     * @return User|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $user = $this->finder->findUserById($id);

        if (! $user) {
            throw new NotFoundHttpException('The requested page does not exist');
        }

        return $user;
    }

}