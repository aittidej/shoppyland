<?php
namespace app\commands;

use yii;
use yii\console\Controller;
use yii\console\ExitCode;

use app\models\OpenOrder;
use app\models\OpenOrderRel;
use app\models\Lot;
use app\models\LotRel;
use app\models\Product;
use app\models\Receipt;
use app\models\Stock;

class TempController extends Controller
{
    public function actionIndex()
	{
		$attachment = "/home/qazxnivkxh1s/public_html/shoppyland/web/uploads/receipt/Document.pdf";
		$parser = new \Smalot\PdfParser\Parser();
		$pdf = $parser->parseFile($attachment);
		$text = $pdf->getText();
		var_dump($text);
	}
	
    public function actionEmail()
    {
		set_time_limit(0);
		$all = Yii::$app->emailReader->getAll();
		$isCapture = false;
		$numberOfItems = 0;
		foreach($all AS $latest)
		{
			$parser = new \Smalot\PdfParser\Parser();
			$pdf = $parser->parseFile($latest['attachment']);
			$text = $pdf->getText();
			$index = $latest['index'];
			$udate = trim($latest['header']->udate);
			$messageId = str_replace("<", "", str_replace(">", "", trim($latest['header']->message_id)));
			$receipt = Receipt::find()->where("message_id='".$messageId."' OR udate=".$udate)->one();
			if(empty($receipt))
				continue;
			
			$text = str_replace(chr(160), " ", str_replace(chr(194), "", $text));
			if(preg_match('/Invoice:\s\d{5}/', $text, $modelText)) 
			{
				if(!empty($modelText[0]))
				{
					$tran = substr($modelText[0], -5);
					var_dump($tran);
					$receipt->transaction_id = $tran;
					$receipt->save(false);
				}
			}
		}
		
        return ExitCode::OK;
    }
	
	public function actionTest()
    {
		set_time_limit(0);
		$count = 0;
		$lotId = 18;
		//$lot = Lot::findOne(17);
		
		$openOrders = OpenOrder::find()->where(['lot_id'=>$lotId])->orderby("open_order_id ASC")->all();
		foreach($openOrders AS $openOrder)
		{
			$openOrderRels = OpenOrderRel::find()->where("open_order_id=$openOrder->open_order_id AND (unit_price IS NULL OR unit_price = 0)")->orderby("open_order_rel_id ASC")->all();
			foreach($openOrderRels AS $openOrderRel)
			{
				$lotRels = LotRel::find()->where(['product_id'=>$openOrderRel->product_id])->all();
				if(!empty($lotRels))
				{
					//var_dump("--".$openOrderRel->product_id);
					if(count($lotRels) > 1)
						$useLotRel = $lotRels[1];
					else
						$useLotRel = $lotRels[0];
					
					if(!empty($useLotRel->overwrite_total))
					{
						var_dump($useLotRel->overwrite_total." - ".$openOrderRel->product_id);
						$openOrderRel->unit_price = $useLotRel->overwrite_total;
						//$openOrderRel->save(false);
					}
					else if(!empty($useLotRel->total))
					{
						var_dump($useLotRel->total." - ".$openOrderRel->product_id);
						$openOrderRel->unit_price = $useLotRel->total;
						//$openOrderRel->save(false);
					}
					else
						var_dump($useLotRel);
				}
				else
					$count += $openOrderRel->qty;
			}
		}
		
		var_dump($count);
		
        return ExitCode::OK;
    }
}
