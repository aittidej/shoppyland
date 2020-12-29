<?php

namespace app\modules\album\controllers;

use yii\web\Controller;
use app\models\UploadFile;
use yii\imagine\Image;
use Imagine\Image\Box;

/**
 * Default controller for the `album` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
		$upload = new UploadFile();
		
		return $this->render('index', ['upload' => $upload]);
    }
	
	public function actionUpload()
    {
		var_dump($_FILES);
		/*$uploadFile = $_FILES['UploadFile'];
		foreach ($uploadFile['name']["image"] as $i=>$name) 
		{
			//empty($folder) ? $targetFile : $folder
			Image::getImagine()
					->open($uploadFile['tmp_name']["image"][$i])
					->thumbnail(new Box($maxSize, $maxSize))
					->save(empty($folder) ? $targetFile : $folder. '/' . $name, ['quality' => $quality]);
		}
		*/
		
		return true;
	}
}
