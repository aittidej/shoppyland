<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

use app\models\LoginForm;
use app\models\SellingList;
use app\models\Product;
use app\models\User;
use app\components\EmailReader;

class WebsiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [];
    }
	
	public function beforeAction($action)   
    {
        $this->layout = "website";
		
        if (parent::beforeAction($action)) {
            if ($this->enableCsrfValidation && Yii::$app->getErrorHandler()->exception === null && !Yii::$app->getRequest()->validateCsrfToken()) {
                throw new BadRequestHttpException(Yii::t('yii', 'Unable to verify your data submission.'));
            }
            
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
	
	public function actionRate()
    {
        return $this->render('rate');
    }
	
	public function actionJobs()
	{
        return $this->render('jobs');
    }
	
	public function actionBrand()
	{
        return $this->render('brand');
    }
	
	public function actionTopProducts()
	{
        return $this->render('top_products');
    }
	
	public function actionOrderStatus()
	{
        return $this->render('order_status');
    }
	
	public function actionShippingInformation()
	{
        return $this->render('shipping_information');
    }
	
	public function actionPartnership()
	{
        return $this->render('partnership');
    }
	
	public function actionPaymentMethods()
	{
        return $this->render('payment_methods');
    }
}
