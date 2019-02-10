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
}
