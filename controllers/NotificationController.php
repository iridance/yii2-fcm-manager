<?php

namespace fcm\manager\controllers;

use Yii;
use fcm\manager\models\FcmNotification;
use fcm\manager\models\searchs\FcmNotification as FcmNotificationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DefaultController implements the CRUD actions for FcmNotifications model.
 */
class NotificationController extends Controller
{
    
    protected $userClass = '';
    /**
     * Set user className method.
     *
     * @param string $value
     * @return self
     */
    public function setUserClass(string $value) : self
    {
        $this->userClass = $value;
        return $this;
    }

    protected $topicClass = '';
    /**
     * Set topic className method.
     *
     * @param string $value
     * @return self
     */
    public function setTopicClass(string $value) : self
    {
        $this->topicClass = $value;
        return $this;
    }

    protected $tokenClass = '';
    /**
     * Set device token className method.
     *
     * @param string $value
     * @return self
     */
    public function setTokenClass(string $value) : self
    {
        $this->tokenClass = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function init()
    {
        parent::init();

        //$this->layout = $this->module->mainLayout;
    }

    /**
     * Lists all FcmNotification models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FcmNotificationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FcmNotification model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new FcmNotification model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FcmNotification();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing FcmNotification model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing FcmNotification model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the FcmNotification model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FcmNotification the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FcmNotification::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
