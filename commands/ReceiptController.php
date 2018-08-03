<?php
namespace app\commands;

use yii;
use yii\console\Controller;


// ./yii receipt
class ReceiptController extends Controller
{

    public function actionIndex()
    {
        $latest = Yii::$app->emailReader->getLatest();
		
		$subject = trim($latest['header']->subject);
		$fromaddress = trim($latest['header']->fromaddress);
		$mailDate = trim($latest['header']->MailDate);
		$body = trim($latest['body']);
		
		//var_dump($subject);
		//var_dump($fromaddress);
		//var_dump($mailDate);
		var_dump($body);
		
		exit;
    }
}
