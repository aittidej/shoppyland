<?php

namespace app\controllers;

use Yii;
use app\models\DiscountList;
use app\models\Lot;
use app\models\LotRel;
use app\models\Product;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for OpenOrder model.
 */
class ReceiptController extends \app\controllers\MainController
{
    public function actionIndex($lotId)
    {
		$latest = Yii::$app->emailReader->getLatest();
		
		$subject = trim($latest['header']->subject);
		$fromaddress = trim($latest['header']->fromaddress);
		$mailDate = trim($latest['header']->MailDate);
		$capture = $this->parseEmail($latest['body']);
		
		$noImageList = [];
		foreach($capture['upc'] AS $index=>$upc)
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
			
			$lotRel = LotRel::findOne(['lot_id'=>$lot_id, 'product_id'=>$product->product_id]);
			if(empty($lotRel))
			{
				$lotRel = New LotRel();
				$lotRel->lot_id = $lotId;
				$lotRel->product_id = $product->product_id;
				$lotRel->price = $capture['price'][$index];
			}
			else
				$lotRel->price = $capture['price'][$index];
			$lotRel->save(false);
		}
		
		if(!empty($noImageList))
		{
			Yii::$app->mailer->compose()
					->setTo('ettidej@gmail.com')
					->setSubject('[PRODUCT FROM RECEIPT] list of products without image')
					->setHtmlBody(json_encode($noImageList))
					->send();
		}
		/*
			'upc' => $upc,
			'model' => $model,
			'title' => $title,
			'price' => $price
		*/
    }
	
	private function parseEmail($body)
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
					
				if(strpos($line, ';') !== false && strpos($line, '/') !== false && strpos($line, '.') !== false)
				{
					$inline = array_map('trim', explode(" ", $line));
					$tempPrice = $inline[(count($inline)-1)];
					$title[$itemNumber] = trim(str_replace($tempPrice, '', $line));
					$price[$itemNumber] = $tempPrice;
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
			
			if (strpos($line, 'CUSTOMER RECEIPT COPY') !== false)
				$startCapture = true;
			
			if (strpos($line, 'www.coach.com/globalprivacy') !== false)
				break;
		}
		
		return [
			'upc' => $upc,
			'model' => $model,
			'title' => $title,
			'price' => $price
		];
	}
}
