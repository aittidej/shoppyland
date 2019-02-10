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
	public $wontInclude = ['mfsrp', 'usd', 'cad', 'our price', 'ns'];
	public $standardSizeFee = ['XS' => 3, 'S' => 4, 'M' => 5, 'L' => 6, 'XL' => 8, 'XXL' => 10];
	
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
    public function actionIndex($q = NULL)
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $q);

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

        if ($model->load(Yii::$app->request->post()) && $model->save()) 
		{
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
			$products = Product::find()->where("title = '' AND model = ''")->indexBy('product_id')->orderby('product_id ASC')->all();
		else
			$products = Product::find()->where(['IN', 'upc', array_values($_GET)])->indexBy('product_id')->orderby('product_id ASC')->all();
		
		if(empty($products))
		{
			Yii::$app->session->setFlash('danger', "No unfinished product!");
			return $this->redirect(['/']);
		}
			
		foreach ($products as $index => $product) {
			$uploads[$index] = new UploadFile();
			$attachments[$index] = new UploadFile();
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
			
			$target_dir = "uploads/tmp/";
			if (!file_exists(addslashes($target_dir)))
				mkdir(addslashes($target_dir), 0777, true);
				
			foreach($attachments AS $product_id=>$attachment)
			{
				$product = Product::findOne($product_id);
				$fileType = strtolower(pathinfo($_FILES["UploadFile"]["name"][$product_id]["attachment"][0], PATHINFO_EXTENSION));
				$target_file = $target_dir . $this->generateRandomString() .'.'.$fileType;
				$attachment->attachment = UploadedFile::getInstances($attachment, "[$product_id]attachment");
				if(!empty($attachment->attachment))
				{
					if($attachment->uploadAttachment(NULL, $target_file))
					{
						$response = $this->uploadToApi($target_file);
						$array = $this->parseCleanUp($response['ParsedResults'][0]['ParsedText'], empty($product->brand) ? NULL : $product->brand->title);
						if(!empty($array['model']))
						{
							$product->model = $array['model'];
							$product->title = $array['title'];
							$product->base_price = $array['base_price'];
							$product->save(false);
						}
					}
				}
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
            'attachments' => $attachments,
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
			
			$FileType = strtolower(pathinfo($_FILES["UploadFile"]["name"]["attachment"], PATHINFO_EXTENSION));
			$target_file = $target_dir . $this->generateRandomString() .'.'.$FileType;
			$upload->attachment = UploadedFile::getInstances($upload, "attachment");
			
			// Check file size
			/*if ($_FILES["UploadFile"]["size"]["attachment"] > 5000000) {
				header('HTTP/1.0 403 Forbidden');
				die("Sorry, your file is too large.");
			}*/
			if($FileType != "pdf" && $FileType != "png" && $FileType != "jpg") {
				header('HTTP/1.0 403 Forbidden');
				die("Sorry, please upload an image file");
			}
			else if(!empty($upload->attachment)) 
			{
				if ($upload->uploadAttachment(NULL, $target_file)) 
				{
					$response = $this->uploadToApi($target_file);
					$array = $this->parseCleanUp($response['ParsedResults'][0]['ParsedText'], empty($product->brand) ? NULL : $product->brand->title);
					//var_dump($array);var_dump($response);exit;
					
					$image->image = UploadedFile::getInstances($image, "image");
					$product->image_path = empty($image->image) ? NULL : $image->uploadMultiImages('images/products/' . $product->product_id . '/');
					if(!empty($array['model']) || !empty($array['title']))
					{
						$product->model = $array['model'];
						$product->title = $array['title'];
						$product->base_price = $array['base_price'];
						$product->save(false);
						
						return $this->redirect(['/product/index']);
					}
					else
					{
						var_dump($response);
						var_dump($array);
						die();
					}
				}
				else 
				{
					header('HTTP/1.0 403 Forbidden');
					die("Sorry, there was an error uploading your file.");
				}
			}
        }
		
		return $this->render('add-products-ocr', [
            'product' => $product,
            'upload' => $upload,
            'image' => $image,
        ]);
    }
	
	public function actionPickSize()
	{
		if (Yii::$app->request->isAjax) 
		{
			$saveOne = $saveTwo = false;
			$openOrder = OpenOrder::findOne($_POST['id']);
			$user = $openOrder->user;
			$product = $this->findModel($_POST['product_id']);
			//$openOrderRels = OpenOrderRel::find()->where(['open_order_id'=>$openOrder->open_order_id, 'product_id'=>$product->product_id])->all();
			$openOrderRel = OpenOrderRel::findone($_POST['open_order_rel_id']);
			
			$laborChargePrice = $user->labor_charge_json;
			$standardSize = array_search($_POST['fee'], $this->standardSizeFee); 
			$personalizeSize = array_search($_POST['fee'], $laborChargePrice); 
			
			if(empty($product->size) && !empty($_POST['fee'])) 
			{
				$product->size = !empty($personalizeSize) ? $personalizeSize : (!empty($standardSize) ? $standardSize : NULL);
				$saveOne = $product->save(false);
			}
			else
			{
				//foreach($openOrderRels AS $openOrderRel){
				if(!empty($personalizeSize) && $personalizeSize == $product->size) // no overwrite, back to personalize size
					$openOrderRel->overwrite_labor = NULL;
				else 
					$openOrderRel->overwrite_labor = $_POST['fee'];
				
				$openOrderRel->free_labor = empty($_POST['fee']) ? 1 : 0;
				$saveTwo = $openOrderRel->save(false);
				//}
			}
			
			return $saveOne || $saveTwo;
		}

		Yii::$app->end();
	}
	
	function parseCleanUp($text, $brand = 'coach')
	{
		$array = ['model' => '','upc' => '','title' => '','base_price' => NULL];
		$title = [];
		$lines = array_map('trim', explode("\n", $text));
		foreach($lines AS $i=>$line)
		{
			$upc = str_replace(' ', '', $line);
			if(is_numeric($upc) && strlen($upc) == 12) { // only number with 12 digits
				$array['upc'] = $upc;
				continue;
			}
			
			switch (strtolower($brand)) 
			{
				case "coach":
					if(preg_match('/\F\d{5}/', $line, $withF)) {
						$array['model'] = $withF[0];
						continue;
					} else if(empty($array['model']) && $i < 2 && preg_match('/\d{5}/', $line, $withoutF)) {
						$array['model'] = $withoutF[0];
						continue;
					}
					break;
				case "michael kors":
					break;
				case "kate spade":
					if(preg_match('/\WKRU\d{4}/', $line, $m)) {
						$array['model'] = $m[0];
						continue;
					} else if(preg_match('/\WLRU\d{4}/', $line, $m)) {
						$array['model'] = $m[0];
						continue;
					}
					break;
			}
			
			if(strpos($line, '$') !== false && is_numeric(str_replace('$', '', $line))) { // with $ sign
				$array['base_price'] = str_replace('$', '', $line);
				continue;
			}
			
			if(!preg_match("/[0-9]+/", $line) == TRUE && !in_array(strtolower($line), $this->wontInclude)) { // no number
				$title[] = $line;
				continue;
			}
		}
		
		if(!empty($title))
		{
			foreach(array_reverse($title) AS $t)
				$array['title'] .= $t." ";
		}
		/*switch (strtolower($brand)) 
		{
			case "coach":

				break;
			case "michael kors":
				break;
		}*/
		
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
		if(empty($response['ErrorMessage'])) {
			return $response;
		} else {
			header('HTTP/1.0 400 Forbidden');
			var_dump($response['ErrorMessage']);
			exit;
		}
		} catch(Exception $err) {
			header('HTTP/1.0 403 Forbidden');
			var_dump($err->getMessage());
			exit;
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
