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
use kartik\mpdf\Pdf;

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
		$openOrderRels = OpenOrderRel::find()->where(['open_order_id'=>$id])->joinWith('product')->orderby('product.model ASC, product.upc ASC')->all();
		
        return $this->render('index', [
            'openOrder' => $openOrder,
            'openOrderRels' => $openOrderRels,
			'print' => false,
        ]);
    }
	
	public function actionFreeLabor($open_order_rel_id, $free)
	{
		if (Yii::$app->request->isPost)
		{
			$openOrderRel = OpenOrderRel::findOne($open_order_rel_id);
			$openOrderRel->free_labor = $free;
			$openOrderRel->save(false);
			
			echo "<script>window.close();</script>";
		}
	}
	
	public function actionPrint($id)
    {
        $openOrder = $this->findModel($id);
		$openOrderRels = OpenOrderRel::find()->where(['open_order_id'=>$id])->joinWith('product')->orderby('product.model ASC')->all();
		
        return $this->renderPartial('index', [
            'openOrder' => $openOrder,
            'openOrderRels' => $openOrderRels,
            'print' => true,
        ]);
    }
	
	public function actionEmail($id)
    {
		if (Yii::$app->request->isPost)
		{
			$openOrder = $this->findModel($id);
			$lot = $openOrder->lot;
			$user = $openOrder->user;
			if(empty($user->email))
				return "No email";
			
			$openOrderRels = OpenOrderRel::find()->where(['open_order_id'=>$id])->joinWith('product')->orderby('product.model ASC')->all();
			$body = $this->renderPartial('index', ['openOrder' => $openOrder,'openOrderRels' => $openOrderRels,'print' => true]);
			/*
			$filename = \Yii::getAlias('@app')."/web/uploads/invoice/".$user->name."_".$lot->lot_number.".pdf";
			$pdf = new Pdf([
					'mode' => Pdf::MODE_BLANK,
					'format' => Pdf::FORMAT_LETTER,
					'orientation' => Pdf::ORIENT_PORTRAIT,
					'destination' => Pdf::DEST_FILE,
					'filename' => $filename,
					'content' => $body,
					//'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
					'cssInline' => 'td, th {border: 1px solid #dddddd;text-align: left;padding: 8px;}table {font-family: arial, sans-serif;font-size: 11px;border-collapse: collapse;width: 100%;}tr:nth-child(even) { background-color: #dddddd; }',
					'options' => ['title' => $user->name."'s Invoice - Lot #".$lot->lot_number],
					'methods' => [
						'SetHeader' => [$user->name."'s Invoice - Lot #".$lot->lot_number . '||' . date('l jS \of F Y h:i A')],
						//'SetFooter' => [ date('l jS \of F Y h:i A')],
					]
				]);
			$pdf->render();*/
			
			$sent = Yii::$app->mailer->compose()
						->setTo($user->email)
						->setCc (["yuwatida85@gmail.com", "ettidej@gmail.com", "billing@shoppylandbyhoney.com"])
						->setSubject($user->name."'s Invoice - Lot #".$openOrder->lot->lot_number)
						->setHtmlBody($body)
						//->attach($filename)
						->send();
			
			if($sent == 1)
			{
				$openOrder->invoice_sent = 1;
				$openOrder->save(false);
			
				//echo "<script>window.close();</script>";
				return $this->redirect(['/openorder/order/index']);
			}
		}
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
        if (($model = OpenOrder::find()->with('user')->with('lot')->where(['open_order_id'=>$id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
