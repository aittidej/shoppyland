<?php

namespace app\controllers\openorder;

use Yii;
use app\models\Brand;
use app\models\OpenOrder;
use app\models\OpenOrderRel;
use app\models\Product;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;

/**
 * OrderController implements the CRUD actions for OpenOrder model.
 */
class ClientController extends Controller
{
	public function actionPreview($token = NULL)
    {
		if(empty($token))
			die("You don't have permission to access this invoice");
		
        $openOrder = OpenOrder::findOne(['token'=>$token]);
		if(empty($openOrder))
			die("You don't have permission to access this invoice");
		
		$openOrderRels = OpenOrderRel::find()->where(['open_order_id'=>$openOrder->open_order_id])->joinWith('product')->orderby('product.model ASC')->all();
		
        return $this->renderPartial('/openorder/report/index', [
            'openOrder' => $openOrder,
            'openOrderRels' => $openOrderRels,
            'print' => true,
            'client' => true,
        ]);
    }
}
