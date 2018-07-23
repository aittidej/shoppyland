<?php

namespace app\controllers\openorder;

use Yii;
use app\models\Brand;
use app\models\OpenOrder;
use app\models\OpenOrderRel;
use app\models\OpenOrderSearch;
use app\models\Product;
use app\models\UploadFile;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for OpenOrder model.
 */
class ReportController extends \app\controllers\MainController
{
    /**
     * Displays a single OpenOrder model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionIndex($id)
    {
        $openOrder = $this->findModel($id);
		$openOrderRels = OpenOrderRel::find()->where(['open_order_id'=>$id])->joinWith('product')->orderby('product.model ASC')->all();
		
        return $this->render('index', [
            'openOrder' => $openOrder,
            'openOrderRels' => $openOrderRels,
        ]);
    }
	
	public function actionPrint($id)
    {
        $openOrder = $this->findModel($id);
		$openOrderRels = OpenOrderRel::find()->where(['open_order_id'=>$id])->joinWith('product')->orderby('product.model ASC')->all();
		
        return $this->renderPartial('print', [
            'openOrder' => $openOrder,
            'openOrderRels' => $openOrderRels,
        ]);
    }
	
    /**
     * Finds the OpenOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OpenOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OpenOrder::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
