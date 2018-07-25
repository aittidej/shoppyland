<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\components\UpcItemDB;
use app\components\eBaySearch;

use app\models\Brand;
use app\models\OpenOrder;
use app\models\OpenOrderRel;
use app\models\OpenOrderSearch;
use app\models\Product;
use app\models\UploadFile;
use app\models\DiscountList;

/**
 * MainController is extended by other controllers in order to check permissions
 */
abstract class MainController extends Controller
{
/**
     * {@inheritdoc}
     */
    public function behaviors()
    {
		return [
			'access' => [
				'class' => \yii\filters\AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
        /*return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];*/
    }
	
    public function init()
    {
		if(Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin)
			return $this->redirect(['/']);
    }
	
	protected function addItemsHelper($items, $openOrderId = FALSE)
	{
		$notFoundList = [];
		foreach($items AS $barcode)
		{
			if(empty($barcode))
				continue;
			
			$invalidUPC = false;
			$barcode = trim($barcode);
			$product = Product::findOne(['upc'=>$barcode, 'status'=>1]);
			
			// Take care of adding missing products
			if(empty($product))
			{
				$upcItemDB = New UpcItemDB();
				$respond = $upcItemDB->getDataByBarcode($barcode);
				if(is_numeric($respond)) // invalid UPC
					$invalidUPC = true;
				else
				{
					$results = json_decode($respond, true);
					if(empty($results['items'])) // UPC not found in UpcItemDB
						$invalidUPC = true;
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
				
				// invalid UPC, product are not yet been created.
				// try with ebay
				if($invalidUPC)
				{
					$eBaySearch = New eBaySearch();
					$respond = $eBaySearch->getDataByBarcode($barcode);
					
					$product = New Product();
					if(empty($respond))
					{
						$product->upc = $barcode;
						$product->save(false);
						
						$notFoundList[$product->product_id] = $barcode;
					}
					else
					{
						$product->upc = $barcode;
						$product->brand_id = $respond['brand_id'];
						$product->model = $respond['model'];
						$product->title = $respond['title'];
						$product->category = $respond['categoryName'];
						$product->image_path = $respond['galleryURL'];
						$product->json_data = $respond['jsonData'];
						$product->save(false);
					}
				}
			}
			
			if(!empty($openOrderId) && !empty($product))
			{
				$openOrderRel = OpenOrderRel::findOne(['open_order_id'=>$openOrderId, 'product_id'=>$product->product_id]);
				if(empty($openOrderRel))
				{
					$openOrderRel = New OpenOrderRel();
					$openOrderRel->open_order_id = $openOrderId;
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
		
		return $notFoundList;
	}
	
	public function priceDiscountCalculator($price, $discountListId)
	{
		$discountList = DiscountList::findOne($discountListId);
		$discounts = $discountList->discount_json;
		foreach($discounts AS $percentage)
			$price = $price*(100-$percentage)/100;
		
		return number_format((is_numeric($price) && $price > 0) ? $price : 0, 2);
	}
}
