<?php

namespace app\controllers;

use Yii;
use app\models\DiscountList;
use app\models\Lot;
use app\models\LotRel;
use app\models\Product;
use app\models\Receipt;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for OpenOrder model.
 */
class ReceiptController extends \app\controllers\MainController
{
    public function actionIndex()
    {
		//$latest = Yii::$app->emailReader->getLatest();
		set_time_limit(0);
		$all = Yii::$app->emailReader->getAll();

		foreach($all AS $latest)
		{
			$noImageList = [];
			$index = $latest['index'];
			$subject = trim(imap_utf8($latest['header']->subject));
			if (strpos(strtolower($subject), 'receipt') !== false)
				continue;
			
			$fromaddress = trim($latest['header']->fromaddress);
			$udate = trim($latest['header']->udate);
			$mailDate = $this->convertTime(trim($latest['header']->MailDate));
			$messageId = str_replace("<", "", str_replace(">", "", trim($latest['header']->message_id)));
			$brandId = $this->brand($fromaddress);
			switch ($brandId) {
				case "1":
					$capture = $this->parseCoachEmail($latest['body']);
					break;
				case "2":
					$capture = $this->parseMichaelKorsEmail($latest['body']);
					break;
				case "3":
					$capture = $this->parseKateSpadeEmail($latest['body']);
					break;
			}
			
			if(!empty($capture))
			{
				$receipt = Receipt::find()->where("message_id='".$messageId."' OR udate=".$udate)->one();			
				if(empty($receipt))
				{
					$receipt = New Receipt();
					$receipt->brand_id = $brandId;
					$receipt->buy_date = $mailDate;
					$receipt->msg_number = $index;
					$receipt->message_id = $messageId;
					$receipt->data = $capture;
					$receipt->udate = $udate;
					$receipt->save(false);
				}
			}
		}
		/*foreach($capture['upc'] AS $index=>$upc)
		{
			if(empty($upc) || empty($capture['price'][$index]))
				continue;
				
			$product = Product::findOne(['upc'=>$upc]);
			if(empty($product))
			{
				$product = New Product();
				$product->upc = $upc;
				$product->model = $capture['model'][$index];
				$product->title = $capture['title'][$index];
				$product->save(false);
				
				$noImageList[] = $upc;
			}
			
			$lotRel = LotRel::findOne(['lot_id'=>$lotId, 'product_id'=>$product->product_id]);
			if(empty($lotRel))
			{
				$lotRel = New LotRel();
				$lotRel->lot_id = $lotId;
				$lotRel->product_id = $product->product_id;
				$lotRel->overwrite_total = $this->roundIt($capture['price'][$index]);
			}
			else
			{
				if(empty($lotRel->overwrite_total))
					$lotRel->overwrite_total = $this->roundIt($capture['price'][$index]);
			}
			$lotRel->save(false);
		}
		
		if(!empty($noImageList))
		{
			Yii::$app->mailer->compose()
					->setTo('ettidej@gmail.com')
					->setSubject('[PRODUCT FROM RECEIPT] list of products without image')
					->setHtmlBody(json_encode($noImageList))
					->send();
		}*/
    }
	
	public function actionTest()
    {
		//$test = Yii::$app->emailReader->delete(0);
		/*$allmails = Yii::$app->emailReader->getAll();
		foreach($allmails AS $latest)
		{
			$subject = trim(imap_utf8($latest['header']->subject));
			$fromaddress = trim($latest['header']->fromaddress);
			$mailDate = trim($latest['header']->MailDate);
			var_dump([
				'subject' => $subject,
				'fromaddress' => $fromaddress,
				'mailDate' => $mailDate,
			]);
		}*/
		
		$latest = Yii::$app->emailReader->getLatest();
		$index = $latest['index'];
		$subject = trim(imap_utf8($latest['header']->subject));
		$fromaddress = trim($latest['header']->fromaddress);
		$udate = trim($latest['header']->udate);
		$mailDate = $this->convertTime(trim($latest['header']->MailDate));
		$messageId = str_replace("<", "", str_replace(">", "", trim($latest['header']->message_id)));
		$brandId = $this->brand($fromaddress);
		$capture = $this->parseCoachEmail($latest['body']);
		
		var_dump($capture);
		//var_dump($latest['body']);
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
					continue;
				}
				
				if(is_numeric($line))
				{
					$upc[$itemNumber] = $line;
					continue;
				}
				
				if(preg_match('/\F\d{5}\s/', $line, $modelText)) {
					$model[$itemNumber] = trim($modelText[0]);
					$itemNumber++;
					continue;
				} else if(preg_match('/^\d{5}\s/', $line, $modelText)) {
					$model[$itemNumber] = trim($modelText[0]);
					$itemNumber++;
					continue;
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
		
		return $data;
	}
	
	
	
	private function parseMichaelKorsEmail($body)
	{
		return;
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
}
