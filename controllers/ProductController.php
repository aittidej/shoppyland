<?php

namespace app\controllers;

use Yii;
use app\models\Brand;
use app\models\OpenOrder;
use app\models\OpenOrderRel;
use app\models\OpenOrderSearch;
use app\models\Product;
use app\models\ProductSearch;
use app\models\UploadFile;
use kartik\file\FileInput;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

use app\components\eBaySearch;
use app\components\UpcItemDB;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends \app\controllers\MainController
{
	public function actionTest()
	{
		$eBaySearch = New eBaySearch();
		$respond = $eBaySearch->getDataByBarcode('191202719316');
		$results = json_decode($respond, true);
		$item = $results['findItemsByKeywordsResponse'][0]['searchResult'][0]['item'];
	}

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
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
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();
		$upload = new UploadFile();
		
        if ($model->load(Yii::$app->request->post()) && $model->save()) 
		{
			$model->status = 1;
			$upload->image = UploadedFile::getInstances($upload, "image");
			if(!empty($upload->image))
				$model->image_path = $upload->uploadMultiImages('images/products/' . $model->product_id . '/');
			else  if(!empty($_POST['Product']['imagPath']))
				$model->image_path = [$_POST['Product']['imagPath']];
			$model->save(false);
			
            return $this->redirect(['view', 'id' => $model->product_id]);
        }

		$model->brand_id = 0;
		$model->weight = 0;
		
        return $this->render('create', [
			'model' => $model,
			'upload' => $upload,
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$upload = new UploadFile();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$model->status = 1;
			$upload->image = UploadedFile::getInstances($upload, "image");
			if(!empty($upload->image))
				$model->image_path = $upload->uploadMultiImages('images/products/' . $model->product_id . '/');
			else if(!empty($_POST['Product']['imagPath']))
				$model->image_path = [$_POST['Product']['imagPath']];
			$model->save(false);
            return $this->redirect(['view', 'id' => $model->product_id]);
        }

        return $this->render('update', [
            'model' => $model,
			'upload' => $upload,
        ]);
    }
	
	public function actionAddProducts()
    {
		if(!empty($_GET['id']))
		{
			$id = $_GET['id'];
			unset($_GET['id']);
			$model = OpenOrder::findOne($id);
		}
		$products = Product::find()->where(['IN', 'upc', array_values($_GET)])->indexBy('product_id')->orderby('product_id ASC')->all();
		foreach ($products as $index => $product) {
			$uploads[$index] = new UploadFile();
		}
		
		set_time_limit(0);
		if (Product::loadMultiple($products, Yii::$app->request->post()) && Product::validateMultiple($products))
		{
			foreach($uploads AS $product_id=>$upload)
			{
				$upload->image = UploadedFile::getInstances($upload, "[$product_id]image");
				if(!empty($upload->image))
					$imagesPath[$product_id] = $upload->uploadMultiImages('images/products/' . $product_id . '/');
			}
			
            foreach ($products as $product) {
				if(!empty($imagesPath[$product->product_id]))
					$product->image_path = $imagesPath[$product->product_id];
				
                $product->save(false);
            }
			
			if(empty($id))
				return $this->redirect(['index']);
			else
				return $this->redirect(['/openorder/order/view', 'id' => $id]);
        }
		
		return $this->render('add-products', [
            'products' => $products,
            'uploads' => $uploads,
        ]);
    }
	
	public function actionAddProductsByUpc()
    {
		$notFoundList = $invalid = [];
        $model = New Product();

        if (Yii::$app->request->isPost)
		{
			set_time_limit(0);
			$items = array_map('trim', explode("\n", $_POST['Product']['items']));
			$notFoundList = $this->addItemsHelper($items);
			
			if(empty($notFoundList))
				return $this->redirect(['index']);
			else
				return $this->redirect(['product/add-products?'.http_build_query($notFoundList)]);
        }

        return $this->render('add-items', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $product = $this->findModel($id);
		if(empty($product->title))
			$product->delete();
		else
		{
			$product->status = 0;
			$product->save(false);
		}
        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
