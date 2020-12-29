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

class ProductController extends Controller
{
	public function actionTest()
    {
		set_time_limit(0);
		$upc = [
				'192643047334','191202239029','192643035362','192643072152',
				'192643042162','889532751822','192643037755','192643035881',
				'191202693012','192643036277','192643044012','191202241992',
				'192643083233','191202404670'
			];
		
		$receipts = Receipt::findAll(['brand_id'=>1]);
		foreach($receipts AS $receipt)
		{
			$datas = $receipt->data;
			foreach($datas['data'] AS $data)
			{
				if (in_array($data['upc'], $upc))
				{
					var_dump($receipt);
					exit;
				}
			}
		}
		
        return ExitCode::OK;
    }
	
	// */5 * * * *  /usr/local/bin/php -q /home/qazxnivkxh1s/public_html/shoppyland_admin/yii product/coach >/dev/null 2>&1
	public function actionCoach()
	{
		$lists = [
					[
						"title" => "Top Handle Pouch In Signature Canvas",
						"url" => "https://www.coachoutlet.com/coach-top-handle-pouch-in-signature-canvas/F58321.html",
						"model" => "F58321"
					]
				];

		foreach($lists AS $data)
		{
			// create curl resource
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $data['url']); // set url
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //return the transfer as a string
			
			$output = curl_exec($ch); // $output contains the output string
			curl_close($ch); // close curl resource to free up system resources

			if (strpos($output, '<div class="invertory-badge out-of-stock ">SOLD OUT</div>') !== false) 
			{
				echo "false\n";
				return false;
			}
			else 
			{
				echo "Avilable Now!!\n";
				return Yii::$app->mailer->compose()
								->setTo(['ettidej@gmail.com', 'yuwatida85@gmail.com'])
								->setSubject($data['model']." is now avilable")
								->setHtmlBody("<a href='".$data['url']."'>".$data['title']." (".$data['model'].")</a> is now avilable")
								->send();
			}
		}
	}
	
	public function actionMk()
	{
		$url = "https://www.michaelkors.com/_/R-US_35F9GFTE3L";

		$ch = curl_init( $url );
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds
		curl_setopt( $ch, CURLOPT_POST, false);
		//curl_setopt( $ch, CURLOPT_POSTFIELDS, []);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false);

		$response = curl_exec( $ch );
		//curl_close($ch);

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if($errno = curl_errno($ch) || $httpCode == 403)
		{
			$error_message = curl_strerror($errno);
			//var_dump($error_message);
			die();
		}

		var_dump($httpCode);
		print_r($response);
	}
}
