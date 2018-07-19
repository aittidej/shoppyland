<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadFile extends Model
{
    /**
     * @var UploadedFile
     */
	public $image;
	public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf, doc, docx, xls, xlsx, csv, txt, rtf, html, zip, jpg, jpeg, png, gif', 'maxFiles' => 5, 'maxSize' => 5 * 1024 * 1024],
			[['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg, jpeg, png, gif', 'maxSize' => 10 * 1024 * 1024, 'maxFiles'=>5],
        ];
    }
	
	public function attributeLabels()
    {
        return [
            'file' => 'File (5MB Max)',
            'image' => 'Image (5MB Max)',
        ];
    }
    
	public function upload()
    {
        if ($this->validate()) {
            $this->file->saveAs('uploads/' . $this->file->baseName . '.' . $this->file->extension);
            return true;
        } else {
            return false;
        }
    }
	
	public function uploadImage($folder)
    {
		if (!file_exists(addslashes($folder)))
			mkdir(addslashes($folder), 0777, true);
		
		$file = $this->image[0];
		if($file->saveAs($folder. '/' . $file->name))
			return true;

		return false;
    }
	
	public function uploadMultiImages($uploadUrl)
    {
		if (!file_exists(addslashes($uploadUrl)))
			mkdir(addslashes($uploadUrl), 0777, true);
		
		$imagesPath = [];
		foreach ($this->image as $image) 
		{
			$imagesPath[] = $uploadUrl . $image->name;
			$image->saveAs($uploadUrl . $image->name);
		}
		
		return $imagesPath;
    }
}

