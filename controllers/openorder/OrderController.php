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

use app\components\UpcItemDB;
use app\components\BarcodeLookup;

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
		$notFoundList = [];
        $model = OpenOrder::findOne($id);
		$allProducts = Product::find()->orderby('brand_id ASC, product_id ASC')->all();

        if (Yii::$app->request->isPost)
		{
			set_time_limit(0);
			$items = array_map('trim', explode("\n", $_POST['OpenOrder']['items']));
			foreach($items AS $barcode)
			{
				if(empty($barcode))
					continue;
				
				$barcode = trim($barcode);
				$product = Product::findOne(['upc'=>$barcode, 'status'=>1]);
				
				// Take care of adding missing products
				if(empty($product))
				{
					$upcItemDB = New UpcItemDB();
					$respond = $upcItemDB->getDataByBarcode($barcode);
					if(is_numeric($respond)) // not valid
					{
						$product = New Product();
						$product->upc = $barcode;
						$product->save(false);
						
						$notFoundList[$product->product_id] = $barcode;
						continue;
					}
					
					$results = json_decode($respond, true);
					if(empty($results['items'])) // UPC not found in UpcItemDB
					{
						$product = New Product();
						$product->upc = $barcode;
						$product->weight = 0;
						$product->save(false);
						
						$notFoundList[$product->product_id] = $barcode;
					}
					else
					{
						foreach($results['items'] AS $item)
						{
							$product = New Product();
							$product->upc = $barcode;
							$product->model = empty($item['model']) ? NULL : $item['model'];
							$product->title = empty($item['title']) ? NULL : $item['title'];
							$product->description = empty($item['description']) ? NULL : $item['description'];
							$product->color = empty($item['color']) ? NULL : $item['color'];
							$product->size = empty($item['size']) ? NULL : $item['size'];
							$product->image_path = empty($item['images']) ? NULL : $item['images'];
							$product->weight = !empty($item['weight']) && is_numeric($item['weight']) ? $item['weight'] : 0;
							$product->dimension = empty($item['dimension']) ? 0 : $item['dimension'];
							if(!empty($item['brand']))
							{
								$brand = Brand::find()->where("LOWER(title)='" . trim(strtolower($item['brand'])) . "'")->one();
								if(empty($brand))
								{
									$brand = New Brand();
									$brand->title = ucfirst(strtolower(trim($item['brand'])));
									$brand->save(false);
								}
								
								$product->brand_id = $brand->brand_id;
							}

							$product->save(false);
						}
					}
				}

				if(!empty($product))
				{
					$openOrderRel = OpenOrderRel::findOne(['open_order_id'=>$id, 'product_id'=>$product->product_id]);
					if(empty($openOrderRel))
					{
						$openOrderRel = New OpenOrderRel();
						$openOrderRel->open_order_id = $id;
						$openOrderRel->product_id = $product->product_id;
						$openOrderRel->qty = 1;
						$openOrderRel->save(false);
					}
					else
					{
						$openOrderRel->qty++;
						$openOrderRel->save(false);
					}
				}
			}
			
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
        //$this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
	
	public function actionTest()
    {
		
		//echo $this->redirect(['add-products', 'id' => 1]).http_build_query($notFoundList);
		/*$upcItemDB = New UpcItemDB();
		//$respond = $upcItemDB->getDataByBarcode('888099705690');
		$respond = $upcItemDB->getDataByBarcode('191202767300');
		$test = json_decode($respond, true);
		
		foreach($test['items'] AS $item)
		{
			var_dump($item['model']);
		}
		*/
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
