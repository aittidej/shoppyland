<?php

namespace app\controllers;

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
		$discountLists = DiscountList::find()->orderby('title ASC')->all();
		
		if (Yii::$app->request->isPost)
		{
			$title = '';
			$allDiscounts = [];
			$discounts = explode(",", $_POST['discounts']);
			foreach($discounts AS $index => $discount)
			{
				if(!is_numeric($discount))
					continue;
					
				$discount = str_replace(' ', '', trim($discount));
				$title .= ($index) ? '% + '.$discount : $discount;
				$allDiscounts[] = $discount;
			}
			$title .= '%';
			
			if(empty($allDiscounts))
				Yii::$app->session->setFlash('danger', "Something wrong!");
			else
			{
				$discountList = New DiscountList();
				$discountList->title = $title;
				$discountList->discount_json = $allDiscounts;
				$discountList->save(false);
				Yii::$app->session->setFlash('success', "Discount saved!");
			}
			
			return $this->redirect(['index']);
		}
		
        return $this->render('index', ['discountLists'=>$discountLists]);
    }
}
