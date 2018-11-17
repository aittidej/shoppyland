<?php

namespace app\controllers;

use Yii;
use app\models\Lot;
use app\models\Receipt;
use app\models\OpenOrder;
use app\models\OpenOrderRel;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for OpenOrder model.
 */
class ReportController extends \app\controllers\MainController
{
	CONST SHIPPING_COST = 610;
	CONST SHIPPING_CHARGE = 780;
	
    public function actionProfit($lotNumber = NULL)
    {
		$lots = Lot::find()->orderby('lot_number DESC')->all();
		if(empty($lotNumber))
		{
			$lot = $lots[0];
			$lotNumber = $lot->lot_number;
		}
		else
			$lot = Lot::findOne(['lot_number'=>$lotNumber]);
		
		$receipts = $lot->allReceipts;
		$openOrders = OpenOrder::find()->with("user")->with('openOrderRels')->where(['lot_id'=>$lot->lot_id])->all();
		$openOrderslist = array_map(function ($entry) { return $entry['open_order_id']; }, $openOrders);
		$openOrderRels = OpenOrderRel::find()
								->with('openOrder')
								->with('product')
								->where(['IN', 'open_order_id', $openOrderslist])
								->asArray()
								->all();
		
		$productsStats = [];
		$numberOfItems = $numberOfBox = $totalCollectible = $totalCollectibleOnlyWithExchange = 0;
		$totalWeightKg = $weightProfitInBaht = $laborCost = $totalCollectibleInBaht = 0;
		foreach($openOrders AS $openOrder)
		{
			$user = $openOrder['user'];
			$totalWeightKg += $openOrder->total_weight;
			$numberOfBox += $openOrder->number_of_box;
			if($user->currency_base == 'USD')
				$weightProfitInBaht += $openOrder->total_weight*(SELF::SHIPPING_CHARGE - SELF::SHIPPING_COST);
		}
		
		foreach($openOrderRels AS $openOrderRel)
		{
			$openOrder = $openOrderRel['openOrder'];
			$product = $openOrderRel['product'];
			$user = $openOrder['user'];
			$numberOfItems += $openOrderRel['qty'];
			
			$unitPrice = ($user['currency_base'] == "USD") ? $openOrderRel['unit_price'] : ($openOrderRel['unit_price']*0.85)/34;
			if(empty($productsStats[$openOrderRel['product_id']]))
			{
				$productsStats[$openOrderRel['product_id']] = [
												'qty' => $openOrderRel['qty'], 
												'cost' => $unitPrice*$openOrderRel['qty'], 
												'product' => $product
											];
			}
			else
			{
				$productsStats[$openOrderRel['product_id']]['qty'] += $openOrderRel['qty'];
				$productsStats[$openOrderRel['product_id']]['cost'] += $unitPrice*$openOrderRel['qty'];
			}
			
			if($user['currency_base'] == "USD")
			{
				if(!empty($product['size']) && !empty($user['labor_charge_json']))
				{
					$laborChargeJson = json_decode($user['labor_charge_json'], true);
					$laborFee = $laborChargeJson[$product['size']];
					if(!$openOrderRel['free_labor'])
						$laborCost += $openOrderRel['qty']*$laborFee;
				}
				
				$totalCollectible += $openOrderRel['unit_price']*$openOrderRel['qty'];
				if($user['payment_method'] == 'Baht')
					$totalCollectibleOnlyWithExchange += $openOrderRel['unit_price']*$openOrderRel['qty'];
			}
			else
				$totalCollectibleInBaht  += $openOrderRel['unit_price']*$openOrderRel['qty'];
		}
		
		return $this->render('profit', [
            'lot' => $lot,
            'lots' => $lots,
			'lotNumber' => $lotNumber,
            'receipts' => $receipts,
            'numberOfItems' => $numberOfItems,
            'totalCollectible' => $totalCollectible,
            'totalCollectibleOnlyWithExchange' => $totalCollectibleOnlyWithExchange,
            'weightProfitInBaht' => $weightProfitInBaht,
            'laborCost' => $laborCost,
            'totalCollectibleInBaht' => $totalCollectibleInBaht,
            'totalWeightKg' => $totalWeightKg,
            'numberOfBox' => $numberOfBox,
            'productsStats' => $productsStats,
            'exchangeRate' => round($this->exchangeRate(), 1)-1,
        ]);
    }
}
