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
        return $this->render('view', [
            'model' => $this->findModel($id),
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
    public function actionUpdate($id = 0)
    {
		if(empty($id))
		{
			$model = Lot::find()->orderby('lot_id DESC')->one();
			if(empty($model)) die('No lot found');
		}
		else
			$model = $this->findModel($id);
			
		$lotRels = LotRel::find()->where(['lot_id'=>$model->lot_id])->with('product')->orderby('lot_rel_id DESC')->all();

        if (Yii::$app->request->isPost)
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
					$lotRel->discount_list_id = $_POST['Lot']['discount_list_id'];
					$lotRel->price = empty($_POST['Lot']['price']) ? NULL : $_POST['Lot']['price'];
					$lotRel->overwrite_total = empty($_POST['Lot']['overwrite_total']) ? NULL : $_POST['Lot']['overwrite_total'];
					$lotRel->save(false);
				}
			}
			
            return $this->redirect(['update', 'id' => $model->lot_id]);
        }

        return $this->render('update', [
            'model' => $model,
            'lotRels' => $lotRels,
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
			if (!empty($_POST['price']) && !empty($_POST['discount_id'])) 
			{
				if(!empty($_POST['lot_rel_id']))
				{
					$lotRel = LotRel::findOne($_POST['lot_rel_id']);
					$lotRel->price = $_POST['price'];
					$lotRel->discount_list_id = $_POST['discount_id'];
					$lotRel->overwrite_total = empty($_POST['overwrite']) ? NULL : $_POST['overwrite'];
					$lotRel->save(false);
				}
				
				if(empty($_POST['overwrite']))
					echo $this->priceDiscountCalculator($_POST['price'], $_POST['discount_id']);
				else
					echo $_POST['overwrite'];
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
		LotRel::deleteAll(['lot_id'=>$id->lot_id]);
        $this->findModel($id)->delete();

        return $this->redirect(['/']);
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
