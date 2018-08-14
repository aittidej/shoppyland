<?php

namespace app\controllers\openorder;

use Yii;
use app\models\Brand;
use app\models\OpenOrder;
use app\models\OpenOrderRel;
use app\models\OpenOrderSearch;
use app\models\Lot;
use app\models\LotRel;
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
        //$openOrder = $this->findModel($id);
        $openOrder = OpenOrder::find()->with('lot')->where(['open_order_id'=>$id])->one();
		$openOrderRelModel = OpenOrderRel::find()->where(['open_order_id'=>$id])->joinWith("product")->orderby("need_attention DESC, product_id ASC, product.model ASC")->asArray()->all(); // ->orderby('need_attention DESC, unit_price ASC, product.upc ASC, product.model ASC')
		$openOrderRels = [];
		
		foreach($openOrderRelModel AS $openOrderRel)
		{
			$openOrderRels[$openOrderRel['product_id']][] = [
						'OpenOrderRelModel' => New OpenOrderRel(),
						'product' => $openOrderRel['product'],
						'openOrderRel' => $openOrderRel,
					];
		}
		
		/*(if (OpenOrderRel::loadMultiple($openOrderRels, Yii::$app->request->post()) && OpenOrderRel::validateMultiple($openOrderRels))
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
        }*/
		
        return $this->render('view', [
            'openOrder' => $openOrder,
            'lot' => $openOrder['lot'],
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
	/*public function actionDelete($id)
    {
		$order = $this->findModel($id);
		OpenOrderRel::deleteAll(['open_order_id'=>$order->open_order_id]);
        $order->delete();

        return $this->redirect(['index']);
    }*/
	
	public function actionDeleteSingle()
	{
	// productId, openOrderId
		if (Yii::$app->request->isAjax) 
		{
			if(!empty($_POST['productId']) && !empty($_POST['openOrderId']))
				return OpenOrderRel::deleteAll([ 'open_order_id'=>$_POST['openOrderId'], 'product_id'=>$_POST['productId'] ]);
			
			return 0;
		}
		
		Yii::$app->end();
	}
	
	public function actionChangePrimarySubtotal()
	{
		if (Yii::$app->request->isAjax) 
		{
			if(!empty($_POST['qty']) && !empty($_POST['price']) && !empty($_POST['openOrderRelId']))
			{
				$openOrderRel = OpenOrderRel::findOne($_POST['openOrderRelId']);
				$openOrderRel->qty = $_POST['qty'];
				$openOrderRel->unit_price = $_POST['price'];
				$openOrderRel->save(false);
				
				return 1;
			}
			else
				return 0;
		}
		
		Yii::$app->end();
	}
	
	public function actionSplitPrice()
	{
		if (Yii::$app->request->isAjax) 
		{
			if(!empty($_POST['openOrderId']) && !empty($_POST['productId']))
			{
				//$product = Product::findOne($_POST['productId']);
				$openOrder = OpenOrder::findOne($_POST['openOrderId']);
				$lotRels = LotRel::find()->where(['lot_id'=>$openOrder->lot_id, 'product_id'=>$_POST['productId']])->all();
				
				$openOrderRel = OpenOrderRel::find()->where(['open_order_id'=>$_POST['openOrderId'], 'product_id'=>$_POST['productId']])->orderby("qty DESC")->one();
				$unitPrice = $openOrderRel->unit_price;
				foreach($lotRels AS $lotRel)
				{
					if($openOrderRel->unit_price != $lotRel->unitPrice)
					{
						$unitPrice = $lotRel->unitPrice;
						break;
					}
				}
			
				$newOpenOrderRel = new OpenOrderRel();
				$newOpenOrderRel->open_order_id = $_POST['openOrderId'];
				$newOpenOrderRel->product_id = $_POST['productId'];
				$newOpenOrderRel->qty = 1;
				$newOpenOrderRel->unit_price = $unitPrice;
				$newOpenOrderRel->save(false);
				
				if($openOrderRel->qty > 1)
				{
					$openOrderRel->qty--;
					if($openOrderRel->qty == 1)
						$openOrderRel->need_attention = 0;
					$openOrderRel->save(false);
				}
				
				return json_encode([
							'newOpenOrderRelId' => $newOpenOrderRel->open_order_rel_id,
							'newUnitPrice' => $newOpenOrderRel->unit_price,
							'newQty' => $newOpenOrderRel->qty,
							'newSubtotal' => $newOpenOrderRel->qty*$newOpenOrderRel->unit_price,
							
							'oldQty' => $openOrderRel->qty,
							'oldSubtotal' => $openOrderRel->qty*$openOrderRel->unit_price,
						]);
			}
		}
		
		Yii::$app->end();
	}
	
	public function actionLoadPrice()
	{
		if (Yii::$app->request->isAjax) 
		{
			if(!empty($_POST['orderId']))
			{
				$needAttention = [];
				$openOrder = OpenOrder::findOne($_POST['orderId']);
				$openOrderRels = $openOrder->openOrderRels;
				foreach($openOrderRels AS $openOrderRel)
				{
					$lotRels = LotRel::find()->where(['lot_id'=>$openOrder->lot_id, 'product_id'=>$openOrderRel->product_id])->all();
					if(empty($lotRels))
						continue;
					
					if(count($lotRels) > 1)
						$openOrderRel->need_attention = 1;
					
					$lotRel = $lotRels[0];
					$unitPrice = empty($lotRel->overwrite_total) ? (empty($lotRel->price) ? NULL : $lotRel->price) : $lotRel->overwrite_total;
					if(empty($openOrderRel->unit_price))
						$openOrderRel->unit_price = $unitPrice;
					else if($openOrderRel->unit_price != $unitPrice)
						$openOrderRel->need_attention = 1;
					$openOrderRel->save(false);
				}
				
				return 1;
			}
		}
		Yii::$app->end();
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
