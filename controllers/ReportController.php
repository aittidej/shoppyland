<?php

namespace app\controllers;

use Yii;
use app\models\Lot;
use app\models\Receipt;
use app\models\OpenOrder;

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
	
    public function actionProfit($lot = NULL)
    {
		if(empty($lot))
			$lot = 54;
		$lot = Lot::findOne(['lot_number'=>$lot]);
		$receipts = $lot->allReceipts;
		$openOrders = OpenOrder::find()->where(['lot_id'=>$lot->lot_id])->with('openOrderRels')->all();
		
		$numberOfItems = $totalCollectible = $totalCollectibleOnlyWithExchange = $weightProfitInBaht = $laborCost = 0;
		foreach($openOrders AS $openOrder)
		{
			$user = $openOrder->user;
			$weightProfitInBaht += $openOrder->total_weight*(SELF::SHIPPING_CHARGE - SELF::SHIPPING_COST);
			foreach($openOrder->openOrderRels AS $openOrderRel)
			{
				$product = $openOrderRel->product;
				$numberOfItems += $openOrderRel->qty;
				$totalCollectible += $openOrderRel->unit_price*$openOrderRel->qty;
				if($user->payment_method == 'Baht')
					$totalCollectibleOnlyWithExchange += $openOrderRel->unit_price*$openOrderRel->qty;
			}
		}
		
		return $this->render('profit', [
            'lot' => $lot,
            'receipts' => $receipts,
            'openOrders' => $openOrders,
            'numberOfItems' => $numberOfItems,
            'totalCollectible' => $totalCollectible,
            'totalCollectibleOnlyWithExchange' => $totalCollectibleOnlyWithExchange,
            'weightProfitInBaht' => $weightProfitInBaht,
            'laborCost' => $laborCost,
        ]);
    }
}
