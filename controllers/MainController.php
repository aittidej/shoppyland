<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\components\UpcItemDB;
use app\components\eBaySearch;

use app\models\Brand;
use app\models\Lot;
use app\models\OpenOrder;
use app\models\OpenOrderRel;
use app\models\OpenOrderSearch;
use app\models\Product;
use app\models\UploadFile;
use app\models\DiscountList;
use app\models\Stock;

/**
 * MainController is extended by other controllers in order to check permissions
 */
abstract class MainController extends Controller
{
	const DEFAULT_EXCHANGE_RATE = 32;

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
	
	public function beforeAction($action)
    {
        if(Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin)
		{
			Yii::$app->session->setFlash('danger', "You do not have sufficient permissions to view this page.");
			return $this->redirect(['/site/login']);
		}

        if (parent::beforeAction($action)) {
            return true;
        }

        return false;
    }
	
	protected function addItemsHelper($items, $openOrderId = FALSE, $data = [], $deductStock = false, $addByProductId = false)
	{
		if(!empty($openOrderId))
			$openOrder = OpenOrder::findOne($openOrderId);
		
		$notFoundList = $lists = [];
		foreach($items AS $item)
		{
			$code = str_replace(' ', '', trim($item));
			if(empty($code) || !is_numeric($code) || (!$addByProductId && strlen($code) != 12 && strlen($code) != 13))
				continue;
			
			if(empty($lists[$code]))
				$lists[$code] = 1;
			else
				$lists[$code]++;
		}

		if(empty($lists))
			return false;

		foreach($lists AS $code=>$numberOfItems)
		{
			$invalidUPC = false;
			$product = $addByProductId ? Product::findOne($code) : Product::findOne(['upc'=>$code]);

			// Take care of adding missing products
			if(empty($product))
			{
				if($addByProductId)
					continue;
				/*$upcItemDB = New UpcItemDB();
				$respond = $upcItemDB->getDataByBarcode($code);
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
							$product->upc = $code;
							$product->model = empty($item['model']) ? NULL : $item['model'];
							$product->title = empty($item['title']) ? NULL : $item['title'];
							$product->description = empty($item['description']) ? NULL : $item['description'];
							$product->color = empty($item['color']) ? NULL : $item['color'];
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
				}*/
				
				// try ebay
				//if($invalidUPC) {
					//$eBaySearch = New eBaySearch();
					//$respond = $eBaySearch->getDataByBarcode($code);
					$respond = false;
					$product = New Product();
					if(empty($respond))
					{
						$product->upc = $code;
						if(!empty($data))
						{
							$product->model = empty($data['model']) ? NULL : $data['model'];
							$product->brand_id = empty($data['brand_id']) ? NULL : $data['brand_id'];
						}
						$product->save(false);
						
						$notFoundList[$product->product_id] = $code;
					}
					else
					{
						$product->upc = $code;
						$product->brand_id = $respond['brand_id'];
						$product->model = $respond['model'];
						$product->title = $respond['title'];
						$product->category = $respond['categoryName'];
						$product->image_path = $respond['galleryURL'];
						$product->json_data = $respond['jsonData'];
						$product->save(false);
					}
				//}
			}
			else
			{
				$product->status = 1;
				$product->save(false);
			}
			
			if(!empty($openOrderId) && !empty($product))
			{
				$openOrderRel = OpenOrderRel::findOne(['open_order_id'=>$openOrderId, 'product_id'=>$product->product_id]);
				if(empty($openOrderRel))
				{
					$lot = $openOrder->lot;
					$user = $openOrder->user;
					$openOrderRel = New OpenOrderRel();
					$openOrderRel->open_order_id = $openOrderId;
					$openOrderRel->product_id = $product->product_id;
					$openOrderRel->qty = $numberOfItems;
					$openOrderRel->unit_price = $user->currency_base == "USD" ? $lot->getUnitPrice($product->product_id) : NULL;
					$openOrderRel->currency = $user->currency_base;
					$openOrderRel->modified_datetime = date('Y-m-d h:i:s');
					//$openOrderRel->subtotal = $openOrderRel->unit_price;
					$openOrderRel->save(false);
				}
				else
				{
					$openOrderRel->qty += $numberOfItems;
					//$openOrderRel->subtotal = $openOrderRel->unit_price*$openOrderRel->qty;
					$openOrderRel->save(false);
				}
				
				if($deductStock)
				{
					$stock = Stock::findOne(['lot_id'=>$openOrder->lot_id, 'product_id'=>$product->product_id]);
					if(!empty($stock))
					{
						$stock->current_qty--;
						$stock->save(false);
					}
				}
			}
		}
		
		return $notFoundList;
	}
	
	public function priceDiscountCalculator($price, $discountListId)
	{
		if(!empty($discountListId))
		{
			$discountList = DiscountList::findOne($discountListId);
			$discounts = $discountList->discount_json;
			foreach($discounts AS $percentage)
				$price = $price*(100-$percentage)/100;
		}
		
		return number_format((is_numeric($price) && $price > 0) ? $this->roundIt($price) : 0, 2);
	}
	
	public function exchangeRate()
	{
		$rate = json_decode(@file_get_contents('http://free.currencyconverterapi.com/api/v5/convert?q=USD_THB&compact=y'), 2);
		return empty($rate['USD_THB']['val']) ? self::DEFAULT_EXCHANGE_RATE : number_format($rate['USD_THB']['val'], 2);
	}
	
	public function roundIt($number, $breakPoint = 0.1)
	{
		$fraction = $number - floor($number);
		return abs(($fraction > $breakPoint) ? ceil($number) : floor($number));
	}
}
