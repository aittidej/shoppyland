<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\Lot;
use app\models\LotSearch;
use app\models\Product;

use app\components\BarcodeReader;

/**
 * LotController implements the CRUD actions for Lot model.
 */
class LotController extends Controller
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
     * Lists all Lot models.
     * @return mixed
     */
    public function actionTest()
	{
		//BarcodeReader
		$Barcode = new BarcodeReader();
		$Barcode->setAppID("shipping "); //use your appID
		$Barcode->setPassword("yveB9ujd1Qnmu4Rz5QF/Y4Og"); //use your password
		$Barcode->setFileName('C:\Users\User\Desktop\barcode1.jpg');
		$Barcode->Read();
		$Result = $Barcode->Result();

		//echo "<img src='images/barcode1.jpg' /><br />";
		//echo "Result: ".
		var_dump($Result);
	}
	
    public function actionIndex()
    {
        $searchModel = new LotSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Lot model.
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
     * Displays a single Lot model.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionModify()
    {
		$model = Lot::find()->orderby('lot_id DESC')->one();
		if(empty($model))
			die('No lot found');
		
		
        return $this->render('view', [
            'model' => $model,
            'id' => $model->lot_id,
        ]);
    }

    /**
     * Creates a new Lot model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Lot();
		$products = Product::find()->where(['brand_id'=>1, 'status'=>1])->orderby('model ASC')->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->lot_id]);
        }

        return $this->render('create', [
            'model' => $model,
            'products' => $products,
        ]);
    }

    /**
     * Updates an existing Lot model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->lot_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Lot model.
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
     * Finds the Lot model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lot the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lot::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
