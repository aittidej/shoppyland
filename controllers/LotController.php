<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\DiscountList;
use app\models\Lot;
use app\models\LotRel;
use app\models\LotSearch;
use app\models\Product;

use app\components\BarcodeReader;

/**
 * LotController implements the CRUD actions for Lot model.
 */
class LotController extends \app\controllers\MainController
{
    /**
     * Lists all Lot models.
     * @return mixed
     */
    public function actionTest()
	{
		echo $this->priceDiscountCalculator(100, 4);
	
		//BarcodeReader
		/*$Barcode = new BarcodeReader();
		$Barcode->setAppID("shipping "); //use your appID
		$Barcode->setPassword("yveB9ujd1Qnmu4Rz5QF/Y4Og"); //use your password
		$Barcode->setFileName('C:\Users\User\Desktop\barcode1.jpg');
		$Barcode->Read();
		$Result = $Barcode->Result();

		//echo "<img src='images/barcode1.jpg' /><br />";
		//echo "Result: ".
		var_dump($Result);*/
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
		$model = $this->findModel($id);
		
		if ($model->load(Yii::$app->request->post()) && $model->save())
		{
			return $this->redirect(['view', 'id' => $model->lot_id]);
		}
		
        return $this->render('view', [
            'model' => $model,
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
        if ($model->load(Yii::$app->request->post()) && $model->save())
		{
			set_time_limit(0);
			$items = array_map('trim', explode("\n", $_POST['Lot']['items']));
			$notFoundList = $this->addItemsHelper($items);
			
			$products = Product::find()->where(['IN', 'upc', array_values($items)])->indexBy('product_id')->orderby('product_id ASC')->all();
			foreach($products AS $product)
			{
				$lotRel = LotRel::findOne(['lot_id'=>$model->lot_id, 'product_id'=>$product->product_id]);
				if(empty($lotRel))
				{
					$lotRel = New LotRel();
					$lotRel->lot_id = $model->lot_id;
					$lotRel->product_id = $product->product_id;
					$lotRel->discount_list_id = empty($_POST['Lot']['discount_list_id']) ? NULL : $_POST['Lot']['discount_list_id'];
					$lotRel->price = empty($_POST['Lot']['price']) ? NULL : $_POST['Lot']['price'];
					$lotRel->save(false);
				}
			}
			
            return $this->redirect(['update', 'id' => $model->lot_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Lot model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id = 0, $brandId = 0)
    {
		if(empty($id))
		{
			$model = Lot::find()->orderby('lot_id DESC')->one();
			if(empty($model))
			{
				Yii::$app->session->setFlash('warning', "No Lot created yet!");
				return $this->redirect(['create']);
			}
		}
		else
			$model = $this->findModel($id);
		
		if(empty($brandId))
			$lotRels = LotRel::find()->where(['lot_id'=>$model->lot_id])->joinWith('product')->orderby('product.brand_id ASC, product.category DESC')->all();
		else	
			$lotRels = LotRel::find()->joinWith('product')->where("lot_id=".$model->lot_id." AND product.brand_id=".$brandId)->orderby('product.brand_id ASC, product.category DESC')->all();

        if (Yii::$app->request->isPost)
		{
			set_time_limit(0);
			$items = array_map('trim', explode("\n", $_POST['Lot']['items']));
			$notFoundList = $this->addItemsHelper($items);
			
			$products = Product::find()->where(['IN', 'upc', array_values($items)])->indexBy('product_id')->orderby('product_id ASC')->all();
			foreach($products AS $product)
			{
				//$lotRel = LotRel::findOne(['lot_id'=>$model->lot_id, 'product_id'=>$product->product_id]);
				//if(empty($lotRel)){
					$lotRel = New LotRel();
					$lotRel->lot_id = $model->lot_id;
					$lotRel->product_id = $product->product_id;
					$lotRel->discount_list_id = $_POST['Lot']['discount_list_id'];
					$lotRel->price = empty($_POST['Lot']['price']) ? NULL : $_POST['Lot']['price'];
					$lotRel->overwrite_total = empty($_POST['Lot']['overwrite_total']) ? NULL : $_POST['Lot']['overwrite_total'];
					$lotRel->save(false);
				//}
			}
			
            return $this->redirect(['update', 'id' => $model->lot_id]);
        }

        return $this->render('update', [
            'model' => $model,
            'lotRels' => $lotRels,
        ]);
    }
	
	public function actionSelectByImage($id = 20)
    {
		$brandid = 1;
		if(empty($id))
		{
			$model = Lot::find()->orderby('lot_id DESC')->one();
			if(empty($model)) die('No lot found');
		}
		else
			$model = $this->findModel($id);
		
		$alreadyIn = $products = [];
		$lotRels = $model->lotRels;
		foreach($lotRels AS $lotRel)
			$alreadyIn[] = $lotRel->product_id;
		
		if (Yii::$app->request->isPost)
		{
			$upc = str_replace(' ', '', $_POST['upc']);
			$product = Product::findOne(['upc'=>$upc]);
			if(!empty($product))
			{
				$lotRel = LotRel::findOne(['lot_id'=>$id, 'product_id'=>$product->product_id]);
				if(empty($lotRel))
				{
					$alreadyIn[] = $product->product_id;
					$lotRel = New LotRel();
					$lotRel->lot_id = $id;
					$lotRel->product_id = $product->product_id;
					$lotRel->save(false);
				}
				
				$products = Product::find()
								->where(['NOT IN', 'product_id', array_values($alreadyIn)])
								->andWhere(['brand_id'=>$brandid])
								->andWhere(['model'=>$product->model])
								->all();
			}
		}
		
		return $this->render('select_by_image', [
			'id' => $id,
			'model' => $model,
            'products' => $products,
		]);
	}
	
	public function actionSelect($id = 0, $brandid = 0)
    {
		$brandid = 1;
		if(empty($id))
		{
			$model = Lot::find()->orderby('lot_id DESC')->one();
			if(empty($model)) die('No lot found');
		}
		else
			$model = $this->findModel($id);
		
		$alreadyIn = [];
		$lotRels = $model->lotRels;
		foreach($lotRels AS $lotRel)
			$alreadyIn[] = $lotRel->product_id;
		
		$products = Product::find()->where(['NOT IN', 'product_id', array_values($alreadyIn)])->andWhere(['brand_id'=>$brandid])->orderby('model ASC')->all();
        if (Yii::$app->request->isPost)
		{

        }

        return $this->render('select', [
            'model' => $model,
            'products' => $products,
        ]);
    }
	
	public function actionPreCalculatePrice()
	{
		if (Yii::$app->request->isAjax) 
		{
			if (!empty($_POST['price']) && !empty($_POST['discount_id'])) 
			{
				echo $this->priceDiscountCalculator($_POST['price'], $_POST['discount_id']);
			}
		}
		
		Yii::$app->end();
	}
	
	public function actionCalculatePrice()
	{
		if (Yii::$app->request->isAjax) 
		{
			if(!empty($_POST['lot_rel_id']))
			{
				$lotRel = LotRel::findOne($_POST['lot_rel_id']);
				$lotRel->price = empty($_POST['price']) ? NULL : $_POST['price'];
				$lotRel->discount_list_id = empty($_POST['discount_id']) ? NULL : $_POST['discount_id'];
				$lotRel->overwrite_total = empty($_POST['overwrite']) ? NULL : $_POST['overwrite'];
				if(empty($lotRel->overwrite_total))
					$lotRel->total = $this->priceDiscountCalculator($_POST['price'], $_POST['discount_id']);
				$lotRel->save(false);
			}
			
			if(empty($_POST['overwrite']))
				echo $this->priceDiscountCalculator($_POST['price'], $_POST['discount_id']);
			else
				echo $_POST['overwrite'];
		}
		
		Yii::$app->end();
	}
	/*
	public function actionSelecteRelatedProduct($lot_id, $product_id)
	{
		$product = Product::findOne($product_id);
		
		$brandid = 1;
		$lotRel = New LotRel();
		$lotRel->lot_id = $lot_id;
		$lotRel->product_id = $product_id;
		$lotRel->save(false);

		$alreadyIn = $products = [];
		$model = $this->findModel($lot_id);
		$lotRels = $model->lotRels;
		foreach($lotRels AS $lotRel)
			$alreadyIn[] = $lotRel->product_id;
		
		$products = Product::find()
						->where(['NOT IN', 'product_id', array_values($alreadyIn)])
						->andWhere(['brand_id'=>$brandid])
						->andWhere(['model'=>$product->model])
						->all();
						
		return $this->render('select_by_image', [
			'id' => $lot_id,
			'model' => $model,
            'products' => $products,
		]);
	}
	*/
	public function actionSelectedProduct()
	{
		if (Yii::$app->request->isAjax) 
		{
			if (!empty($_POST['lot_id']) && !empty($_POST['product_id'])) 
			{
				/*if(empty($_POST['isCheck']))
				{
					$lotRel = LotRel::findOne(['lot_id'=>$_POST['lot_id'], 'product_id'=>$_POST['product_id']]);
					$lotRel->delete();
				}
				else
				{*/
					$lotRel = New LotRel();
					$lotRel->lot_id = $_POST['lot_id'];
					$lotRel->product_id = $_POST['product_id'];
					//$lotRel->discount_list_id = empty($_POST['discount_id']) ? NULL : $_POST['discount_id'];
					//$lotRel->price = empty($_POST['price']) ? NULL : $_POST['price'];
					//$lotRel->total = $this->priceDiscountCalculator($_POST['price'], $_POST['discount_id']);
					$lotRel->save(false);
				//}
			}
		}
		
		Yii::$app->end();
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
		LotRel::deleteAll(['lot_id'=>$id]);
        $this->findModel($id)->delete();

        return $this->redirect(['/']);
    }
	
	/**
     * Deletes an existing Lot model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionLotRelDelete()
    {
		if (Yii::$app->request->isAjax) 
		{
			if (!empty($_POST['lotRelId'])) 
			{
				$lotRel = LotRel::findOne($_POST['lotRelId']);
				return $lotRel->delete();
			}
		}
		
		Yii::$app->end();
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
