<?php

namespace app\controllers\openorder;

use Yii;
use app\models\OpenOrder;
use app\models\OpenOrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for OpenOrder model.
 */
class OrderController extends Controller
{
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

    /**
     * Lists all OpenOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OpenOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OpenOrder model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($open_order_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($open_order_id),
        ]);
    }

    /**
     * Creates a new OpenOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OpenOrder();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['add-items', 'open_order_id' => $model->open_order_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
	
    /**
     * Updates an existing OpenOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->open_order_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
	
	// https://www.upcitemdb.com/info-coach_handbags-wallets
	// https://www.upcitemdb.com/info-coach-handbag
	// https://www.barcodelookup.com
	public function actionAddItems($open_order_id)
    {
        $model = OpenOrder::findOne($open_order_id);

        if (Yii::$app->request->isPost)
		{
			
			
            return $this->redirect(['view', 'id' => $model->open_order_id]);
        }

        return $this->render('add-items', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing OpenOrder model.
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
     * Finds the OpenOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OpenOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OpenOrder::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
