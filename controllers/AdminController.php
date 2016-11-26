<?php

namespace atans\user\controllers;

use atans\user\models\User;
use atans\user\traits\AjaxValidationTrait;
use Yii;
use atans\user\Finder;
use atans\user\traits\ModuleTrait;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class AdminController extends Controller
{
    use AjaxValidationTrait;
    use ModuleTrait;

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
        $model = $this->findModel($id);
        $model->setScenario(User::SCENARIO_UPDATE);

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been updated'));

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionAssignments($id)
    {
        if (! isset(Yii::$app->extensions['atans/yii2-rbac'])) {
            throw new NotFoundHttpException();
        }

        $model = $this->findModel($id);

        return $this->render('_assignments', [
            'model' => $model,
        ]);
    }

    public function actionView($id)
    {
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