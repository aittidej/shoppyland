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
		
		//$baseRate = json_decode($lot->rate_json, true);
		$baseRate = $lot->rate_json;
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
		$totalWeightKg = $weightProfitInBaht = $laborCost = $totalCollectibleInBaht = $totalBathQty = 0;
		foreach($openOrders AS $openOrder)
		{
			$user = $openOrder['user'];
			$isUsd = empty($openOrder->currency_base) ? $user->currency_base == "USD" : $openOrder->currency_base == "USD";
			$totalWeightKg += $openOrder->total_weight;
			$numberOfBox += $openOrder->number_of_box;
			if($isUsd) 
			{
				$shippingCharge = empty($baseRate['shipping_charge']) ? SELF::SHIPPING_CHARGE : $baseRate['shipping_charge'];
				$shippingCost = empty($baseRate['shipping_cost']) ? SELF::SHIPPING_COST : $baseRate['shipping_cost'];
				$weightProfitInBaht += $openOrder->total_weight*($shippingCharge - $shippingCost);
			}
		}
		
		foreach($openOrderRels AS $openOrderRel)
		{
			$openOrder = $openOrderRel['openOrder'];
			$user = $openOrder['user'];
			$isUsd = empty($openOrder['currency_base']) ? $user['currency_base'] == "USD" : $openOrder['currency_base'] == "USD";
			$paymentMethod = empty($openOrder['payment_method']) ? $user['payment_method'] : $openOrder['payment_method'];
			$product = $openOrderRel['product'];
			$numberOfItems += $openOrderRel['qty'];
			$unitPrice = ($isUsd) ? $openOrderRel['unit_price'] : ($openOrderRel['unit_price']*0.85)/34;
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
			
			if($isUsd)
			{
				if(!empty($product['size']) && !empty($user['labor_charge_json']))
				{
					$laborChargeJson = json_decode($user['labor_charge_json'], true);
					$laborFee = $laborChargeJson[$product['size']];
					if(!$openOrderRel['free_labor'])
						$laborCost += $openOrderRel['qty']*$laborFee;
				}
				
				$totalCollectible += $openOrderRel['unit_price']*$openOrderRel['qty'];
				if($paymentMethod == 'Baht')
					$totalCollectibleOnlyWithExchange += $openOrderRel['unit_price']*$openOrderRel['qty'];
			}
			else 
			{
				if($openOrderRel['unit_price'] > 800)
					$totalBathQty += $openOrderRel['qty'];
				
				$totalCollectibleInBaht  += $openOrderRel['unit_price']*$openOrderRel['qty'];
			}
		}
		$exchangeRate = empty($baseRate['sell_exchange_rate']) ? (round($this->exchangeRate(), 1)-1) : $baseRate['sell_exchange_rate'];
		return $this->render('profit', [
            'lot' => $lot,
            'lots' => $lots,
			'lotNumber' => $lotNumber,
            'baseRate' => $baseRate,
            'receipts' => $receipts,
            'numberOfItems' => $numberOfItems,
            'totalCollectible' => $totalCollectible,
            'totalCollectibleOnlyWithExchange' => $totalCollectibleOnlyWithExchange,
            'weightProfitInBaht' => $weightProfitInBaht,
            'laborCost' => $laborCost,
            'totalBathQty' => $totalBathQty,
            'totalCollectibleInBaht' => $totalCollectibleInBaht,
            'totalWeightKg' => $totalWeightKg,
            'numberOfBox' => $numberOfBox,
            'productsStats' => $productsStats,
            'exchangeRate' => $exchangeRate,
        ]);
    }
}
