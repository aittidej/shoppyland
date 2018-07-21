<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

/**
 * MainController is extended by other controllers in order to check permissions
 */
abstract class MainController extends Controller
{
/**
     * {@inheritdoc}
     */
    public function behaviors()
    {
		return [
			'access' => [
				'class' => \yii\filters\AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
        /*return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];*/
    }
	
    public function init()
    {
		if(Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin)
			return $this->redirect(['/']);
    }
}
