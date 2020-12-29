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

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionDashboard()
    {
		if (Yii::$app->user->isGuest) {
			//return $this->goHome();
			return $this->redirect(['/site/login']);
        }
		
		$user = Yii::$app->user->identity;
		
		if ($user->load(Yii::$app->request->post()) && $user->save()) 
		{
			return $this->redirect(['dashboard']);
		}
		
        return $this->render('dashboard', ['user'=>$user]);
    }
	
	public function actionIndex()
    {
		if (Yii::$app->user->isGuest)
			return $this->redirect(['/site/login']);
		else
			return $this->redirect(['/site/dashboard']);
        /*
		$this->layout = "website";
		\Yii::$app->view->registerMetaTag([
			'name' => 'description',
			'content' => Yii::$app->name . ' is your personal shopper located in US. We help you buy anything from store and/or online and ship worldwide right to your doorstep.'
		]);
		
		if (Yii::$app->request->isPost)
		{
			if(!empty($_POST["g-recaptcha-response"]))
			{
				$response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LdT7awUAAAAAA_pJkGVe2XdziZyNdALGSpu3-Rj&response=".$_POST["g-recaptcha-response"]."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
				if($response['success'] == true)
				{
					Yii::$app->mailer->compose()
						->setTo('service@shoppylandbyhoney.com')
						->setSubject('[Website] Contact Us Form')
						->setHtmlBody($_POST['name']."<br>".$_POST['email']."<br>".$_POST['phone']."<br><br>".$_POST['message'])
						->send();
					return $this->redirect(['/', 'contactussuccess'=>1]);
				}
			}
			
			return $this->redirect(['/', 'contactusfailed'=>1]);
		}
		
		$hightlightProducts = Product::find()->where("highlight=1 AND image_path IS NOT NULL")->orderby('random()')->limit('3')->all();
		$sellingLists = SellingList::find()->where(['status'=>1])->with('product')->orderby('selling_list_id DESC')->limit('12')->all();

        return $this->render('index', ['hightlightProducts'=>$hightlightProducts, 'sellingLists'=>$sellingLists]);
		*/
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
			//return $this->goHome();
			return $this->redirect(['/site/dashboard']);
        }
		
		$this->layout = "login";

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
			//return $this->goBack();
			return $this->redirect(['/site/dashboard']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
	
	public function actionResetPassword($token)
	{
		$this->layout = "login";
		
		$error = false;
		$user = User::findOne(['token'=>$token]);
        if(empty($user))
			return $this->redirect(['/site/dashboard']);
			
		if (Yii::$app->request->isPost)
		{
			if($_POST['password'] != $_POST['confirm_password'])
				$error = 'Password does not match the confirm password.';
			else if(strlen($_POST['password']) < 6)
				$error = 'Password cannot be less than 6 characters.';
			else
			{
				$user->password = Yii::$app->passwordhash->create_hash($_POST["password"]);
				$user->save(false);
				
				Yii::$app->session->setFlash('success', "Successfully reset password, please login now.");
				return $this->redirect(['/site/login']);
			}
		}
			
		return $this->render('reset', [
			'user' => $user,
			'token' => $token,
			'error' => $error,
		]);
		
    }
}
