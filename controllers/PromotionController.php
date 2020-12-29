<?php

namespace app\controllers;

use Yii;
use app\models\DiscountList;
use app\models\PromotionCode;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for OpenOrder model.
 */
class PromotionController extends Controller
{
	public $layout = 'login';
	
    public function actionIndex($coupon = false)
    {
		$ip = $this->getUserIpAddr();
		$promotionCode = PromotionCode::find()->where("ip_address ilike '%".$ip."%'")->one();
		if(empty($promotionCode))
		{
			$promotionCode = new PromotionCode();
			$promotionCode->ip_address = $ip;
			$promotionCode->discount_amount = 100;
			$promotionCode->save(false);
			
			$promotionCode->code = Yii::$app->passwordhash->generateToken((6-strlen($promotionCode->promotion_code_id)), true).$promotionCode->promotion_code_id;
			$promotionCode->save(false);
		}
		$detail = '';
		
		return $this->render('index', ['promotionCode'=>$promotionCode, 'detail'=>$detail]);
	}
	
	public function actionPen($coupon = false)
    {
		$ip = $this->getUserIpAddr();
		$promotionCode = PromotionCode::find()->where("ip_address ilike '%".$ip."%'")->one();
		if(empty($promotionCode))
		{
			$promotionCode = new PromotionCode();
			$promotionCode->ip_address = $ip;
			$promotionCode->discount_amount = 1000;
			$promotionCode->save(false);
			
			$promotionCode->code = Yii::$app->passwordhash->generateToken((6-strlen($promotionCode->promotion_code_id)), true).$promotionCode->promotion_code_id;
			$promotionCode->save(false);
		}
		$detail = 'โค้ดส่วนลด ฿'.number_format($promotionCode->discount_amount).' เมื่อซื้อสินค้าที่ร่วมรายการขั้นต่ำ ฿20,000';
		
		return $this->render('index', ['promotionCode'=>$promotionCode, 'detail'=>$detail]);
	}
	
	private function getCodeGenerator()
	{
		
	}
	
	public function getUserIpAddr()
	{
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			//ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			//ip pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		$ip = explode(',', $ip);
		if(count($ip) > 1)
			array_pop($ip);
		
		return $ip[0];
	}
}
