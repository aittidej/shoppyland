<?php
namespace app\commands;

use yii;
use yii\console\Controller;
use app\components\eBaySearch;
use app\models\DiscountList;
use app\models\Lot;
use app\models\LotRel;
use app\models\Product;
use app\models\Receipt;

class ReceiptController extends Controller
{
	// /home/qazxnivkxh1s/www/shoppyland/yii receipt/test
	public function actionTest()
	{
		$price = 86;
		$lotRel = LotRel::findOne(['lot_id'=>10, 'product_id'=>164, 'overwrite_total'=>$price]);
		var_dump($lotRel);
	}
	
	// /home/qazxnivkxh1s/www/shoppyland/yii receipt/map-price
	public function actionMapPrice()
	{
		$this->mapPrice();
	}
	
	// /home/qazxnivkxh1s/www/shoppyland/yii receipt/pull-receipt-from-email
	public function actionPullReceiptFromEmail()
    {
		date_default_timezone_set('America/Los_Angeles');
		set_time_limit(0);
		$all = Yii::$app->emailReader->getAll();
		$isCapture = false;
		$numberOfItems = 0;
		foreach($all AS $latest)
		{
			$index = $latest['index'];
			$udate = trim($latest['header']->udate);
			$messageId = str_replace("<", "", str_replace(">", "", trim($latest['header']->message_id)));
			$receipt = Receipt::find()->where("message_id='".$messageId."' OR udate=".$udate)->one();
			if(!empty($receipt))
				continue;
			
			$subject = trim(imap_utf8($latest['header']->subject));
			if (!(strpos(strtolower($subject), 'receipt') !== false))
				continue;

			$mailDate = $this->convertTime(trim($latest['header']->MailDate));
			$fromaddress = trim($latest['header']->fromaddress);
			$brandId = $this->brand($fromaddress);
			switch ($brandId) {
				case "1":
					$capture = $this->parseCoachEmail($latest['body']);
					break;
				case "2":
					$capture = $this->parseMichaelKorsEmail($latest['fetchbody']);
					break;
				case "3":
					$capture = $this->parseKateSpadeEmail($latest['body']);
					break;
			}
			
			if(!empty($capture))
			{
				$receipt = New Receipt();
				$receipt->brand_id = $brandId;
				$receipt->buy_date = $mailDate;
				$receipt->msg_number = $index;
				$receipt->message_id = $messageId;
				$receipt->data = $capture['data'];
				$receipt->udate = $udate;
				$receipt->number_of_items = $capture['numberOfItems'];
				$receipt->save(false);
				
				$isCapture = true;
			}
		}
		
		if($isCapture)
			$this->mapPrice();
    }
	
	private function mapPrice()
    {
		$noImageList = [];
		$receipts = Receipt::find()->where(['unread'=>1])->all();
		foreach($receipts AS $receipt)
		{
			$buyDate = $receipt->buy_date;
			$lot = Lot::find()->where("(end_date IS NULL OR end_date > '$buyDate') AND start_date <= '$buyDate'")->one();
			//var_dump($lot);exit;
			if(empty($lot))
			{
				$lastLot = Lot::find()->orderby("lot_id DESC")->one();
				$lot = new Lot();
				$lot->lot_number = $lastLot->lot_number+1;
				$lot->start_date = $buyDate;
				$lot->save(false);
			}
			
			foreach($receipt->data AS $index=>$data)
			{
				$product = Product::findOne(['upc'=>$data['upc']]);
				if(empty($product))
				{
					$noImageList[] = $data['upc'];
					$product = New Product();
					$product->upc = $data['upc'];
					$product->title = $data['title'];
					$product->brand_id = $receipt->brand_id;
					
					$eBaySearch = New eBaySearch();
					$respond = $eBaySearch->getDataByBarcode($data['upc']);
					if(!empty($respond['galleryURL']))
						$product->image_path = $respond['galleryURL'];
				}
				else if(strlen($product->title) < 30)
					$product->title = $data['title'];
				
				if(empty($product->price))
					$product->base_price = empty($data['fullPrice']) ? NULL : $data['fullPrice'];
				$product->model = $data['model'];
				$product->save(false);
				
				$overwriteTotal = $this->roundIt($data['price']);
				//$lotRel = LotRel::findOne(['lot_id'=>$lot->lot_id, 'product_id'=>$product->product_id, 'overwrite_total'=>$overwriteTotal]);
				$lotRel = LotRel::find()->where("lot_id=".$lot->lot_id." AND product_id=".$product->product_id." AND (total=".$overwriteTotal." OR overwrite_total=".$overwriteTotal.")")->one();
				if(empty($lotRel))
				{
					$lotRel = New LotRel();
					$lotRel->lot_id = $lot->lot_id;
					$lotRel->product_id = $product->product_id;
					$lotRel->bought_date = $buyDate;
					$lotRel->overwrite_total = $overwriteTotal;
					$lotRel->save(false);
				}
			}
			
			$receipt->unread = 0;
			$receipt->save(false);
		}
		
		if(!empty($noImageList))
		{
			Yii::$app->mailer->compose()
					->setTo('ettidej@gmail.com')
					->setSubject('[PRODUCT FROM RECEIPT] list of products without image')
					->setHtmlBody(json_encode($noImageList))
					->send();
		}
	}
	
	private function parseCoachEmail($body)
	{
		$itemNumber = 0;
		$startCapture = false;
		$bodyArray = array_map('trim', explode("\n", $body));
		foreach($bodyArray AS $line)
		{
			$line = str_replace('=20', '', $line);
			$line = trim(preg_replace('/\s\s+/', ' ', $line));
			if(empty($line))
				continue;
				
			if (strpos($line, '**BEGIN RETURN**') !== false) // skip all return
			{
				$startCapture = false;
				continue;
			}

			if($startCapture)
			{
				if(empty($title[$itemNumber]))
					$title[$itemNumber] = '';
				if(empty($price[$itemNumber]))
					$price[$itemNumber] = '';
				if(empty($upc[$itemNumber]))
					$upc[$itemNumber] = '';
				if(empty($model[$itemNumber]))
					$model[$itemNumber] = '';
					
				if(strpos($line, '-') !== false && strpos($line, '$') !== false && strpos($line, '.') !== false)
				{
					$inline = array_map('trim', explode(" ", $line));
					$tempPrice = $inline[(count($inline)-1)];
					$title[$itemNumber] = trim(str_replace($tempPrice, '', $line));
					$price[$itemNumber] = str_replace("$", "", str_replace("T", "", $tempPrice));
				}
				else if(is_numeric($line))
					$upc[$itemNumber] = $line;
				else if(preg_match('/\F\d{5}\s/', $line, $modelText)) 
				{
					$model[$itemNumber] = trim($modelText[0]);
					$itemNumber++;
				} 
				else if(preg_match('/^\d{5}\s/', $line, $modelText)) 
				{
					$model[$itemNumber] = trim($modelText[0]);
					$itemNumber++;
				}
			}
			
			if (!$startCapture && strpos($line, '------------------------------------------') !== false)
				$startCapture = true;
			else if ($startCapture && strpos($line, 'SUBTOTAL') !== false)
				break;
		}

		$data = [];
		foreach($upc AS $i=>$barcode)
		{
			if(empty($barcode))
				continue;
			
			$data[$i] = [
				'upc' => $barcode,
				'model' => $model[$i],
				'title' => $title[$i],
				'price' => $price[$i],
			];
		}
		
		return ['data'=>$data, 'numberOfItems'=>$itemNumber];
	}
	
	private function parseMichaelKorsEmail($body)
	{
		$itemNumber = 0;
		$startCapture = false;
		$bodyArray = array_map('trim', explode("\n", $body));
		foreach($bodyArray AS $line)
		{			
			if(empty($line))
				continue;
			
			if($startCapture)
			{
				if(empty($title[$itemNumber]))
					$title[$itemNumber] = '';
				if(empty($price[$itemNumber]))
					$price[$itemNumber] = '';
				if(empty($fullPrice[$itemNumber]))
					$fullPrice[$itemNumber] = '';
				if(empty($upc[$itemNumber]))
					$upc[$itemNumber] = '';
				if(empty($model[$itemNumber]))
					$model[$itemNumber] = '';
				
				if(preg_match('/^\d{12}\s/', $line, $upcText))
				{
					$textArray = array_values(array_filter(array_map('trim', explode(" ", $line))));
					if(is_numeric(trim($upcText[0])))
						$upc[$itemNumber] = trim($upcText[0]);
					
					if(!empty($textArray[1]))
						$title[$itemNumber] = $textArray[1];
					
					if(!empty($textArray[2]))
						$fullPrice[$itemNumber] = str_replace("$", "", str_replace("T", "", $textArray[2]));
				}
				else if(strpos($line, 'Style') !== false)
					$model[$itemNumber] = trim(str_replace("Style:", "", $line));
				else if(strpos($line, 'New Price') !== false)
				{
					$price[$itemNumber] = trim(str_replace("$", "", str_replace("New Price:", "", $line)));
					$itemNumber++;
				}
			}
			
			if (!$startCapture && strpos($line, 'Salesperson') !== false)
				$startCapture = true;
			else if ($startCapture && strpos($line, 'SUBTOTAL') !== false)
				break;
		}
		
		$data = [];
		foreach($upc AS $i=>$barcode)
		{
			if(empty($barcode))
				continue;
			
			$data[$i] = [
				'upc' => $barcode,
				'model' => $model[$i],
				'title' => $title[$i],
				'price' => $price[$i],
				'fullPrice' => $fullPrice[$i],
			];
		}

		return ['data'=>$data, 'numberOfItems'=>$itemNumber];
	}
	
	private function parseKateSpadeEmail($body)
	{
		return;
	}
	
	private function brand($fromaddress)
	{
		if(strpos($fromaddress, 'Coach') !== false)
			return 1;
		else if(strpos($fromaddress, 'Michael Kors') !== false)
			return 2;
		else if(strpos($fromaddress, 'katespade') !== false)
			return 3;
	}
	
	private function convertTime($time)
	{
		$dt = new \DateTime($time, new \DateTimeZone('UTC'));
		$dt->setTimezone(new \DateTimeZone('America/Los_Angeles'));
		return $dt->format('Y-m-d H:i:s');
	}
	
	public function roundIt($number, $breakPoint = 0.1)
	{
		$fraction = $number - floor($number);
		return ($fraction > $breakPoint) ? ceil($number) : floor($number);
	}
}
