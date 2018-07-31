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
		var_dump($respond);
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
            return $this->redirect(['index']);
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
		
		if(empty($_GET))
			$products = Product::find()->where("title = ''")->indexBy('product_id')->orderby('product_id ASC')->all();
		else
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
	
	public function actionAddProductsOcr($id)
    {
		$product = Product::findOne($id);
		$upload = new UploadFile();
		$image = new UploadFile();
		
		set_time_limit(0);
		if (Yii::$app->request->isPost)
		{
			$target_dir = "uploads/tmp/";
			if (!file_exists(addslashes($target_dir)))
				mkdir(addslashes($target_dir), 0777, true);
			
			$uploadOk = 1;
			$FileType = strtolower(pathinfo($_FILES["UploadFile"]["name"]["attachment"], PATHINFO_EXTENSION));
			$target_file = $target_dir . $this->generateRandomString() .'.'.$FileType;
			// Check file size
			if ($_FILES["UploadFile"]["size"]["attachment"] > 10000000) {
				header('HTTP/1.0 403 Forbidden');
				echo "Sorry, your file is too large.";
				$uploadOk = 0;
			}
			if($FileType != "pdf" && $FileType != "png" && $FileType != "jpg") {
				header('HTTP/1.0 403 Forbidden');
				echo "Sorry, please upload a pdf file";
				$uploadOk = 0;
			}
			if ($uploadOk == 1) {

				if (move_uploaded_file($_FILES["UploadFile"]["tmp_name"]["attachment"], $target_file)) 
				{
					$response = $this->uploadToApi($target_file);
					$array = $this->parseCleanUp($response['ParsedResults'][0]['ParsedText'], empty($product->brand) ? NULL : $product->brand->title);
//var_dump($array);var_dump($response);exit;
					if(!empty($array['model']))
					{
						$product->model = $array['model'];
						$product->title = $array['title'];
						//$product->base_price = $array['base_price'];
						$image->image = UploadedFile::getInstances($image, "image");
						if(!empty($image->image))
							$product->image_path = $image->uploadMultiImages('images/products/' . $product->product_id . '/');
						else  if(!empty($_POST['Product']['imagPath']))
							$product->image_path = [$_POST['Product']['imagPath']];
						$product->save(false);
						
						return $this->redirect(['/product/index']);
					}
					else
						die(var_dump($response));
				} 
				else 
				{
					header('HTTP/1.0 403 Forbidden');
					echo "Sorry, there was an error uploading your file.";exit;
				}
			}exit; 
        }
		
		return $this->render('add-products-ocr', [
            'product' => $product,
            'upload' => $upload,
            'image' => $image,
        ]);
    }
	
	
	function parseCleanUp($text, $brand = 'coach')
	{
		$array = ['model' => '','upc' => '','title' => '','base_price' => ''];
		$lines = array_map('trim', explode("\n", $text));
		switch (strtolower($brand)) 
		{
			case "coach":
				if(!empty($lines[0]) && (preg_match('/^F[0-9]{5}$/', $lines[0]) || preg_match('/^f[0-9]{5}$/', $lines[0]) || preg_match('/^[0-9]{5}$/', $lines[0])))
					$array['model'] = $lines[0];
				if(!empty($lines[2]))
				{
					$upc = str_replace(' ', '', $lines[2]);
					if(is_numeric($upc) && strlen($upc) == 12)
						$array['upc'] = $upc;
				}
				if(!empty($lines[3]))
					$array['title'] .= $lines[3].' ';
				if(!empty($lines[1]))
					$array['title'] .= $lines[1];
				$array['base_price'] = empty($lines[6]) ? NULL : str_replace('$', '', $lines[6]);
				break;
			case "michael kors":
				break;
			default:
				echo "Your favorite color is neither red, blue, nor green!";
		}
		
		return $array;
	}
	
	function uploadToApi($target_file)
	{
		$fileData = fopen($target_file, 'r');
		$client = new \GuzzleHttp\Client();
		try {
			$r = $client->request('POST', 'http://api.ocr.space/parse/image', [
				'headers' => ['apiKey' => '0fa99465ac88957'],
				'query' => ['isOverlayRequired' => TRUE],
				'multipart' => [
					[
						'name' => 'file',
						'contents' => $fileData,
					],
				],
			], 
			['file' => $fileData]
		);
		$response =  json_decode($r->getBody(),true);
		if($response['ErrorMessage'] == "") {
			return $response;
		} else {
			header('HTTP/1.0 400 Forbidden');
			var_dump($response['ErrorMessage']);
		}
		} catch(Exception $err) {
			header('HTTP/1.0 403 Forbidden');
			echo $err->getMessage();
		}
	}
	
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	public function actionAddProductsByUpc()
    {
		$notFoundList = $invalid = [];
        $model = New Product();

		if (Yii::$app->request->isPost)
		{
			set_time_limit(0);
			$items = array_map('trim', explode("\n", $_POST['Product']['items']));
			$notFoundList = $this->addItemsHelper($items, FALSE, $_POST['Product']);
			
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
