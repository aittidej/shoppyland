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
use app\models\Stock;

class ReceiptController extends Controller
{
	// /home/qazxnivkxh1s/www/shoppyland_admin/yii receipt/test
	public function actionTest()
	{
		//$all = Yii::$app->emailReader->getAll();
		//var_dump($all[0]);
		
		$all = Yii::$app->emailReader->getAll();
		foreach($all AS $latest)
		{
			var_dump($latest['attachment']);

			$parser = new \Smalot\PdfParser\Parser();
			
			try {
				$pdf = $parser->parseFile($latest['attachment']);
			} 
			catch (Exception $e) {
				if ($e->getMessage()) {
					echo 'Returned: '.$e->getMessage();
					return FALSE;
				}
			}
			//$pdf = $parser->parseFile($latest['attachment']);
			$text = $pdf->getText();
			
			var_dump($pdf);
			
			exit;
		}
		
		/*$attachment = "/home/qazxnivkxh1s/public_html/shoppyland_admin/web/uploads/attachment/Customer_2019-10-14_205558993.pdf";
		$parser = new \Smalot\PdfParser\Parser();
		$pdf = $parser->parseFile($attachment);
		$text = $pdf->getText();*/
	}
	
	public function actionCheckStock()
	{
		$receipts = $lot = [];
		//$receipts = Receipt::find()->where("receipt_id>=94 AND receipt_id<=108")->all();
		//$lot = Lot::findOne(18);
		foreach($receipts AS $receipt)
		{
			foreach($receipt->data AS $data)
			{
				if(empty($data['upc']))
					continue;
					
				$product = Product::findOne(['upc'=>$data['upc']]);
				if(empty($product))
					continue;
				
				echo $data['upc']."\n";
				
				$stock = Stock::findOne(['lot_id'=>$lot->lot_id, 'product_id'=>$product->product_id]);
				if(empty($stock))
				{
					$stock = New Stock();
					$stock->lot_id = $lot->lot_id;
					$stock->product_id = $product->product_id;
					$stock->qty = 1;
					$stock->current_qty = 1;
					$stock->save(false);
				}
				else
				{
					$stock->qty++;
					$stock->current_qty++;
					$stock->save(false);
				}
			}
		}
		
	}
	
	// /home/qazxnivkxh1s/www/shoppyland_admin/yii receipt/map-price
	public function actionMapPrice()
	{
		$this->mapPrice();
	}
	
	// /home/qazxnivkxh1s/www/shoppyland_admin/yii receipt/pull-receipt-from-email shoppyland shop@buchoo.com
	public function actionPullReceiptFromEmail()
    {
		date_default_timezone_set('America/Los_Angeles');
		set_time_limit(0);
		$all = Yii::$app->emailReader->getAll();
		$isCapture = false;
		$numberOfItems = 0;
		foreach($all AS $latest)
		{
			$tranId = $capture = NULL;
			$index = $latest['index'];
			$udate = trim($latest['header']->udate);
			$messageId = str_replace("<", "", str_replace(">", "", trim($latest['header']->message_id)));
			$receipt = Receipt::find()->where("message_id='".$messageId."' OR udate=".$udate)->one();
			if(!empty($receipt))
				continue;
			
			$subject = trim(imap_utf8($latest['header']->subject));
			$from = $latest['header']->from;
			if(!empty($from[0]) && !empty($from[0]->mailbox))
			{
				$mailbox = $from[0]->mailbox;
				if($mailbox == 'MKO.00137')
				{
					echo Yii::$app->mailer->compose()
							->setTo('ettidej@gmail.com')
							->setSubject($subject)
							->setHtmlBody($latest['body'])
							->send();
					continue;
				}
			}
			else if (!(strpos(strtolower($subject), 'receipt') !== false))
				continue;
			$mailDate = $this->convertTime(trim($latest['header']->MailDate));
			$fromaddress = trim($latest['header']->fromaddress);
			$brandId = $this->brand($fromaddress, $subject);
			switch ($brandId) 
			{
				case "1":
					if(preg_match('/TRAN:\s\d{6}/', $latest['body'], $tranIdList)) 
					{
						if(!empty($tranIdList[0]))
							$tranId = substr($tranIdList[0], -6);
					}
					$capture = $this->parseCoachEmail($latest['body']);
					break;
				case "2":
					break;
				/*
					//if(preg_match('/Trans\s\d{6}/', $latest['body'], $tranIdList))
					if(preg_match('/\d{20}/', $latest['body'], $tranIdList)) 
					{
						if(!empty($tranIdList[0]))
							$tranId = substr($tranIdList[0], -20);
					}
					
					$capture = $this->parseMichaelKorsEmail($latest['body']);
					break;
				*/
				case "3":
					if(!empty($latest['attachment']))
					{
						$parser = new \Smalot\PdfParser\Parser();
						$pdf = $parser->parseFile($latest['attachment']);
						$text = $pdf->getText();
						$capture = $this->parseKateSpadeEmail($text);
						
						$text = str_replace(chr(160), " ", str_replace(chr(194), "", $text));
						if(preg_match('/Invoice:\s\d{5}/', $text, $tranIdList)) 
						{
							if(!empty($tranIdList[0]))
								$tranId = substr($tranIdList[0], -5);
						}
					}
					break;
			}
			
			if(!empty($capture) && !empty($brandId))
			{
				$receipt = New Receipt();
				$receipt->brand_id = $brandId;
				$receipt->buy_date = $mailDate;
				$receipt->msg_number = $index;
				$receipt->message_id = $messageId;
				$receipt->data = $capture;
				$receipt->udate = $udate;
				$receipt->transaction_id = $tranId;
				$receipt->number_of_items = $capture['numberOfItems'];
				$receipt->total = empty($capture['total']) ? 0 : $capture['total'];
				$receipt->save(false);
				
				$isCapture = true;
			}
		}
		
		if($isCapture)
			return $this->mapPrice();
		
		return false;
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
			
			$array = $receipt->data;
			foreach($array['data'] AS $index=>$data)
			{
				$product = Product::findOne(['upc'=>$data['upc']]);
				if(empty($product))
				{
					$noImageList[] = $data['upc'];
					$product = New Product();
					$product->upc = $data['upc'];
					
					$eBaySearch = New eBaySearch();
					$respond = $eBaySearch->getDataByBarcode($data['upc']);
					if(!empty($respond['galleryURL']))
						$product->image_path = $respond['galleryURL'];
				}
				
				if(strlen($product->title) < 30)
					$product->title = $data['title'];
				
				if(empty($product->price))
					$product->base_price = empty($data['fullPrice']) ? NULL : abs($data['fullPrice']);
				
				$product->brand_id = $receipt->brand_id;
				$product->model = $data['model'];
				$product->save(false);
				
				$boughtPrice = $this->roundIt($data['price']);
				//$lotRel = LotRel::findOne(['lot_id'=>$lot->lot_id, 'product_id'=>$product->product_id, 'boughtPrice'=>$boughtPrice]);
				$lotRel = LotRel::find()->where("lot_id=".$lot->lot_id." AND product_id=".$product->product_id." AND (total=".$boughtPrice." OR overwrite_total=".$boughtPrice.")")->one();
				if(empty($lotRel))
				{
					$lotRel = New LotRel();
					$lotRel->lot_id = $lot->lot_id;
					$lotRel->product_id = $product->product_id;
					$lotRel->bought_date = $buyDate;
					$lotRel->bought_price = $boughtPrice;
					$lotRel->overwrite_total = $boughtPrice;
					$lotRel->save(false);
				}
				else if(empty($lotRel->bought_date))
				{
					$lotRel->bought_date = $buyDate;
					$lotRel->bought_price = $boughtPrice;
					$lotRel->save(false);
				}
				
				$stock = Stock::findOne(['lot_id'=>$lot->lot_id, 'product_id'=>$product->product_id]);
				if(empty($stock))
				{
					$stock = New Stock();
					$stock->lot_id = $lot->lot_id;
					$stock->product_id = $product->product_id;
					$stock->qty = 1;
					$stock->current_qty = 1;
					$stock->save(false);
				}
				else
				{
					$stock->qty++;
					$stock->current_qty++;
					$stock->save(false);
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
		
		return true;
	}
	
	private function parseCoachEmail($body)
	{
		$itemNumber = $total = 0;
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
				
				if((strpos($line, ';') !== false || strpos($line, '/') !== false) && strpos($line, '$') !== false && strpos($line, '.') !== false)
				{
					$inline = array_map('trim', explode(" ", $line));
					$tempPrice = $inline[(count($inline)-1)];
					$title[$itemNumber] = trim(str_replace($tempPrice, '', $line));
					$price[$itemNumber] = str_replace("$", "", str_replace("T", "", $tempPrice));
					
				}
				else if(is_numeric($line))
					$upc[$itemNumber] = $line;
				else if(preg_match('/\F\d{5}\s/', $line, $modelText) || preg_match('/\FG\d{4}\s/', $line, $modelText) || preg_match('/\C\d{4}\s/', $line, $modelText) || preg_match('/^\d{5}\s/', $line, $modelText) || preg_match('/^\d{4}\s/', $line, $modelText)) 
				{
					$model[$itemNumber] = trim($modelText[0]);
					$itemNumber++;
				}
			}
			
			if (!$startCapture && strpos($line, '------------------------------------------') !== false)
				$startCapture = true;
			else if ($startCapture && strpos($line, 'SUBTOTAL') !== false)
			{
				$startCapture = false;
				continue;
			}
			else if (strpos($line, 'TOTAL') !== false)
			{
				$inline = str_replace("TOTAL", "", $line);
				$total = trim(str_replace("$", "", str_replace("T", "", str_replace(",", "", $inline))));
				if ( strpos($line, '(') !== false && strpos($line, ')') !== false )
					$total = trim(str_replace("(", "", str_replace(")", "", $total))*(-1));
				break;
				/*
				$inline = array_map('trim', explode(" ", $line));
				if(empty($inline[1]))
					break;
				
				$total = str_replace("$", "", str_replace("T", "", str_replace(",", "", $inline[1])));
				if ( strpos($line, '(') !== false && strpos($line, ')') !== false )
					$total = str_replace("(", "", str_replace(")", "", $total))*(-1);
				break;*/
			}
		}
		
		if(empty($upc))
			return false;
		
		$data = [];
		foreach($upc AS $i=>$barcode)
		{
			if(empty($barcode))
				continue;
				
			if ( strpos($price[$i], '(') !== false && strpos($price[$i], ')') !== false )
				$price[$i] = trim(str_replace("(", "", str_replace(")", "", $price[$i]))*(-1));
			
			$data[$i] = [
				'upc' => $barcode,
				'model' => $model[$i],
				'title' => $title[$i],
				'price' => $price[$i],
			];
		}
		
		return ['data'=>$data, 'numberOfItems'=>$itemNumber, 'total' => number_format($total, 2, '.', '')];
	}
	
	
	// /home/qazxnivkxh1s/www/shoppyland_admin/yii receipt/pull-mk-receipt shoppyland
	public function actionPullMkReceipt()
	{
		$brandId = 2; // MK
		$index = 10;
		//$attachment = "/home/qazxnivkxh1s/www/shoppyland_admin/web/uploads/attachment/receipt($index)_unlocked.pdf";
		$attachment = "/home/qazxnivkxh1s/www/shoppyland_admin/web/uploads/attachment/receipt(9)(1).pdf";
		$buyDate = '2020-03-05 9:38 PM';
		
		$parser = new \Smalot\PdfParser\Parser();
		$pdf = $parser->parseFile($attachment);
		$text = $pdf->getText();
		
		if(preg_match('/Trans \d{6}/', $text, $tranIdList))
		{
			$tranIdList = str_replace('Trans ', '', $tranIdList);
			$capture = $this->parseMichaelKorsEmail($text);
			var_dump($capture);exit;
			/*if(!empty($capture) && !empty($brandId) && !empty($tranIdList[0]))
			{
				$receipt = New Receipt();
				$receipt->brand_id = $brandId;
				$receipt->buy_date = $buyDate;
				$receipt->msg_number = $index;
				//$receipt->message_id = $messageId;
				$receipt->data = $capture;
				$receipt->udate = strtotime($buyDate); //(int)"157712844".$index;
				$receipt->transaction_id = $tranIdList[0];
				$receipt->number_of_items = $capture['numberOfItems']/2;
				$receipt->total = empty($capture['total']) ? 0 : $capture['total'];
				
				echo "\n";
				$save = $receipt->save(false);
				echo $save;
				echo "\n";
			}*/
		}
		
		if($save)
			$this->mapPrice();
	}
	
	private function parseMichaelKorsEmail($body)
	{
		$itemNumber = $total = 0;
		$startCapture = false;
		$bodyArray = array_map('trim', explode("\n", $body));
		foreach($bodyArray AS $i=>$line)
		{
			$line = str_replace('S=', '', $line);
			$line = str_replace('=', '', $line);
			$line = trim(preg_replace('/\s\s+/', ' ', $line));
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
				
				if(preg_match('/^\d{12}/', $line, $upcText))
				{
					$textArray = array_values(array_filter(array_map('trim', explode(" ", $line))));
					//$total = str_replace(" ", "", $total);
					if(is_numeric(trim($upcText[0])))
						$upc[$itemNumber] = trim($upcText[0]);
					
					if(!empty($textArray[1]))
						$title[$itemNumber] = $textArray[1];
					
					if(!empty($textArray[2]))
					{
						if(!empty($textArray[3]))
							$fullPrice[$itemNumber] = str_replace(" ", "", str_replace("$", "", str_replace("T", "", $textArray[2].$textArray[3])));
						else
							$fullPrice[$itemNumber] = str_replace("$", "", str_replace("T", "", $textArray[2]));
					}
				}
				else if(strpos(str_replace(' ', '', $line), 'Style:') !== false)
					$model[$itemNumber] = str_replace(" ", "", str_replace("Style:", "", $line));
				else if(strpos($line, 'New Price:') !== false)
				{
					$price[$itemNumber] = trim(str_replace("$", "", str_replace("NewPrice:", "", str_replace(' ', '', $line))));
					$itemNumber++;
				}
			}
			
			if (!$startCapture && strpos($line, '------------------------------------------') !== false) {
				$startCapture = true;
			}else if ($startCapture && strpos($line, 'SUBTOTAL') !== false)
			{
				$startCapture = false;
				continue;
			}
			else if (strpos($line, 'TOTAL') !== false)
			{
				$inline = str_replace("TOTAL", "", str_replace(" ", "", $line));
				$total = trim(str_replace("$", "", str_replace("T", "", str_replace(",", "", $inline))));
				if ( strpos($line, '(') !== false && strpos($line, ')') !== false )
					$total = trim(str_replace("(", "", str_replace(")", "", $total))*(-1));
				break;
			}
		}
		
		$data = [];
		foreach($upc AS $i=>$barcode)
		{
			if(empty($barcode))
				continue;
				
			if ( strpos($fullPrice[$i], '(') !== false && strpos($fullPrice[$i], ')') !== false )
				$fullPrice[$i] = trim(str_replace("(", "", str_replace(")", "", $fullPrice[$i]))*(-1));
			if ( strpos($price[$i], '(') !== false && strpos($price[$i], ')') !== false )
				$price[$i] = trim(str_replace("(", "", str_replace(")", "", $price[$i]))*(-1));
			
			$data[$i] = [
				'upc' => $barcode,
				'model' => $model[$i],
				'title' => $title[$i],
				'price' => $price[$i],
				'fullPrice' => $fullPrice[$i],
			];
		}
		$total = str_replace(" ", "", $total);
		return ['data'=>$data, 'numberOfItems'=>$itemNumber, 'total' => number_format($total, 2, '.', '')];
	}
	
	private function oldParseMichaelKorsEmail($body)
	{
		$itemNumber = $total = 0;
		$startCapture = false;
		$bodyArray = array_map('trim', explode("\n", $body));
		foreach($bodyArray AS $line)
		{
			$line = str_replace('S=', '', $line);
			$line = str_replace('=', '', $line);
			$line = trim(preg_replace('/\s\s+/', ' ', $line));
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
					{
						if(!empty($textArray[3]))
							$fullPrice[$itemNumber] = str_replace(" ", "", str_replace("$", "", str_replace("T", "", $textArray[2].$textArray[3])));
						else
							$fullPrice[$itemNumber] = str_replace("$", "", str_replace("T", "", $textArray[2]));
					}
				}
				else if(strpos(str_replace(' ', '', $line), 'Style') !== false)
					$model[$itemNumber] = trim(str_replace("Style:", "", $line));
				else if(strpos(str_replace(' ', '', $line), 'NewPrice') !== false)
				{
					$price[$itemNumber] = trim(str_replace("$", "", str_replace("NewPrice:", "", str_replace(' ', '', $line))));
					$itemNumber++;
				}
			}
			
			if (!$startCapture && strpos($line, '------------------------------------------') !== false)
				$startCapture = true;
			else if ($startCapture && strpos($line, 'SUBTOTAL') !== false)
			{
				$startCapture = false;
				continue;
			}
			else if (strpos($line, 'TOTAL') !== false)
			{
				$inline = str_replace("TOTAL", "", str_replace(" ", "", $line));
				$total = trim(str_replace("$", "", str_replace("T", "", str_replace(",", "", $inline))));
				if ( strpos($line, '(') !== false && strpos($line, ')') !== false )
					$total = trim(str_replace("(", "", str_replace(")", "", $total))*(-1));
				break;
				/*$inline = array_map('trim', explode(" ", $line));
				if(empty($inline[1]))
					break;
				
				$total = str_replace("$", "", str_replace("T", "", str_replace(",", "", $inline[1])));
				if ( strpos($line, '(') !== false && strpos($line, ')') !== false )
					$total = str_replace("(", "", str_replace(")", "", $total))*(-1);
				break;*/
			}
		}
		
		if(empty($upc))
		{
			/*foreach($bodyArray AS $num=>$line)
			{
				var_dump($line);
				
				if($num > 100)
					exit;
			}*/
			var_dump($body);
			exit;
		}
		
		$data = [];
		foreach($upc AS $i=>$barcode)
		{
			if(empty($barcode))
				continue;
				
			if ( strpos($fullPrice[$i], '(') !== false && strpos($fullPrice[$i], ')') !== false )
				$fullPrice[$i] = trim(str_replace("(", "", str_replace(")", "", $fullPrice[$i]))*(-1));
			if ( strpos($price[$i], '(') !== false && strpos($price[$i], ')') !== false )
				$price[$i] = trim(str_replace("(", "", str_replace(")", "", $price[$i]))*(-1));
			
			$data[$i] = [
				'upc' => $barcode,
				'model' => $model[$i],
				'title' => $title[$i],
				'price' => $price[$i],
				'fullPrice' => $fullPrice[$i],
			];
		}

		return ['data'=>$data, 'numberOfItems'=>$itemNumber, 'total' => number_format($total, 2, '.', '')];
	}
	
	private function parseKateSpadeEmail($body)
	{
		$itemNumber = 0;
		$startCapture = false;
		$bodyArray = array_map('trim', explode("\n", str_replace(chr(160), " ", str_replace(chr(194), "", $body))));
		foreach($bodyArray AS $line)
		{	
			if(empty($line))
				continue;
			
			if($startCapture)
			{
				$qty = 1;
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
				
				if(ctype_alpha(str_replace(' ', '', trim($line))) !== false)
					$title[$itemNumber] = trim($line);
				if(preg_match('/^\d{12}\s/', $line))
				{
					$lineArrays = array_values(array_filter(explode(" ", $line)));
					if(!empty($lineArrays[0]) && strlen($lineArrays[0]) == 12 && is_numeric($lineArrays[0]))
					{
						$upc[$itemNumber] = $lineArrays[0];
						$fullPrice[$itemNumber] = str_replace("$", "", $lineArrays[2]);
						$price[$itemNumber] = str_replace("$", "", $lineArrays[3]);
						$itemNumber += $lineArrays[1];
					}
				}
			}
			
			if (!$startCapture && strpos($line, 'Item') !== false  && strpos($line, 'Qty') !== false  && strpos($line, 'Price') !== false  && strpos($line, 'Amount') !== false)
				$startCapture = true;
			else if (strpos($line, 'Total USD') !== false)
			{
				$inline = str_replace("Total USD", "", $line);
				$total = trim(str_replace("$", "", str_replace("T", "", str_replace(",", "", $inline))));
				if ( strpos($line, '(') !== false && strpos($line, ')') !== false )
					$total = trim(str_replace("(", "", str_replace(")", "", $total))*(-1));
				break;
			}
			else if ($startCapture && strpos($line, '________________________________________________') !== false)
				break;
		}
		
		$data = [];
		foreach($upc AS $i=>$barcode)
		{
			if(empty($barcode))
				continue;
				
			if ( strpos($fullPrice[$i], '(') !== false && strpos($fullPrice[$i], ')') !== false )
				$fullPrice[$i] = trim(str_replace("(", "", str_replace(")", "", $fullPrice[$i]))*(-1));
			if ( strpos($price[$i], '(') !== false && strpos($price[$i], ')') !== false )
				$price[$i] = trim(str_replace("(", "", str_replace(")", "", $price[$i]))*(-1));
			
			$data[$i] = [
				'upc' => $barcode,
				'model' => $model[$i],
				'title' => $title[$i],
				'price' => $price[$i],
				'fullPrice' => $fullPrice[$i],
			];
		}

		return ['data'=>$data, 'numberOfItems'=>$itemNumber, 'total' => number_format($total, 2, '.', '')];
	}
	
	private function brand($fromaddress, $subject = false)
	{
		if(strpos($fromaddress, 'Coach') !== false)
			return 1;
		else if(strpos($fromaddress, 'Michael Kors') !== false)
			return 2;
		else if(strpos($fromaddress, 'katespade') !== false)
			return 3;
		else if(strpos(strtolower($subject), 'coach') !== false && strpos(strtolower($subject), 'receipt') !== false)
			return 1;
		else if(strpos(strtolower($subject), 'kate spade') !== false && strpos(strtolower($subject), 'receipt') !== false)
			return 3;
			
		return false;
	}
	
	private function convertTime($time)
	{
		$dt = new \DateTime($time, new \DateTimeZone('UTC'));
		$dt->setTimezone(new \DateTimeZone('America/Los_Angeles'));
		return $dt->format('Y-m-d H:i:s');
	}
	
	public function roundIt($number, $breakPoint = 0.1)
	{
		if(!is_numeric($number))
			return 0;
		
		$fraction = $number - floor($number);
		return ($fraction > $breakPoint) ? ceil($number) : floor($number);
	}
}
