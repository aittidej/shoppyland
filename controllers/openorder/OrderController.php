<?php

namespace app\controllers\openorder;

use Yii;
use app\models\Brand;
use app\models\OpenOrder;
use app\models\OpenOrderRel;
use app\models\OpenOrderSearch;
use app\models\Product;
use app\models\UploadFile;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for OpenOrder model.
 */
class OrderController extends \app\controllers\MainController
{
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
    public function actionView($id)
    {
        $openOrder = $this->findModel($id);
		$openOrderRels = OpenOrderRel::find()->where(['open_order_id'=>$id])->joinWith('product')->orderby('product.model ASC')->all();
		
		if (OpenOrderRel::loadMultiple($openOrderRels, Yii::$app->request->post()) && OpenOrderRel::validateMultiple($openOrderRels))
		{
            foreach ($openOrderRels as $openOrderRel) 
			{
				if(empty($openOrderRel->qty))
				{
					$openOrderRel->delete();
					continue;
				}
				
				if(empty($openOrderRel->unit_price))
					$openOrderRel->unit_price = 0;
					
				$openOrderRel->subtotal = $openOrderRel->qty*$openOrderRel->unit_price;
                $openOrderRel->save(false);
            }
			
            return $this->redirect(['/openorder/order/view', 'id' => $id]);
        }
		
        return $this->render('view', [
            'openOrder' => $openOrder,
            'openOrderRels' => $openOrderRels,
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
            return $this->redirect(['add-items', 'id' => $model->open_order_id]);
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
	public function actionAddItems($id)
    {
        $model = OpenOrder::findOne($id);
		$allProducts = Product::find()->orderby('brand_id ASC, product_id ASC')->all();

        if (Yii::$app->request->isPost)
		{
			set_time_limit(0);
			$items = array_map('trim', explode("\n", $_POST['OpenOrder']['items']));
			$notFoundList = $this->addItemsHelper($items, $id);
			
			if(empty($notFoundList))
				return $this->redirect(['view', 'id' => $model->open_order_id]);
			else
				return $this->redirect(['product/add-products?id='.$id.'&'.http_build_query($notFoundList)]);
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
		$order = $this->findModel($id);
		OpenOrderRel::deleteAll(['open_order_id'=>$order->open_order_id]);
        $order->delete();

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
