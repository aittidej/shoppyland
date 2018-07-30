<?php

namespace app\controllers\openorder;

use Yii;
use app\models\DiscountList;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for OpenOrder model.
 */
class DiscountController extends \app\controllers\MainController
{
    public function actionIndex()
    {
		if (Yii::$app->request->isPost)
		{
			$title = '';
			$dis = [];
			$discounts = explode(",", $_POST['discounts'])
			foreach($discounts AS $index => $discount)
			{
				if(count($discounts) > $index)
				$title .= $discount'% + ';
				$dis[] = $discount;
			}
			
			$discountList = New DiscountList();
			$discountList->title = $title;
			$discountList->discount_json = json_encode($dis);
			$discountList->save(false);
		}
		
        return $this->render('index');
    }
}
