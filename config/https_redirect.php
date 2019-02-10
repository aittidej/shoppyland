<?php 

class HttpsRedirect extends \yii\web\Application
{
	public function handleRequest($request)
	{
		//check if connection is secure
		if (!isset($_SERVER['HTTP_FRONT_END_HTTPS']) && !(strpos($request->absoluteUrl, 'https') !== false)) 
		{
			//otherwise redirect to same url with https
			$secureUrl = str_replace('http', 'https', $request->absoluteUrl);
			
			//use 301 for a permanent redirect
			return Yii::$app->getResponse()->redirect($secureUrl, 301);
		} else {
			//if secure connection call parent implementation
			return parent::handleRequest($request);
		}
	}
}
