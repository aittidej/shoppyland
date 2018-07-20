<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

/**
 * MainController is extended by other controllers in order to check permissions
 */
abstract class MainController extends Controller
{
    public function init()
    {
		if(!Yii::$app->user->identity->isAdmin)
			return $this->redirect(['/']);
    }
}
