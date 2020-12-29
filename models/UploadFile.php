<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

use yii\imagine\Image;
use Imagine\Image\Box;

class UploadFile extends Model
{
    /**
     * @var UploadedFile
     */
	public $attachment;
	public $file;
	public $image;

    public function rules()
    {
        return [
            [['attachment'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf, doc, docx, xls, xlsx, csv, txt, rtf, html, zip, jpg, jpeg, png, gif', 'maxFiles' => 10, 'maxSize' => 10 * 1024 * 1024],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf, doc, docx, xls, xlsx, csv, txt, rtf, html, zip, jpg, jpeg, png, gif', 'maxFiles' => 10, 'maxSize' => 10 * 1024 * 1024],
			[['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg, jpeg, png, gif', 'maxSize' => 10 * 1024 * 1024, 'maxFiles'=>10],
        ];
    }
	
	public function attributeLabels()
    {
        return [
            'file' => 'File (10MB Max)',
            'image' => 'Image (10MB Max)',
            'attachment' => 'Attachment (10MB Max)',
        ];
    }
    
	/*public function upload()
    {
        if ($this->validate()) {
            $this->file->saveAs('uploads/' . $this->file->baseName . '.' . $this->file->extension);
            return true;
        } else {
            return false;
        }
    }*/
	
	public function uploadAttachment($folder, $targetFile = '', $maxSize = 500, $quality = 90)
    {
		if (!empty($folder) && !file_exists(addslashes($folder)))
			mkdir(addslashes($folder), 0777, true);
		
		foreach ($this->attachment as $attachment) 
		{
			Image::getImagine()
					->open($attachment->tempName)
					->thumbnail(new Box($maxSize, $maxSize))
					->save(empty($folder) ? $targetFile : $folder. '/' . $file->name, ['quality' => $quality]);
		}
		
		return true;
    }
	
	public function uploadMultiImages($uploadUrl, $maxSize = 500, $quality = 90)
    {
		if (!file_exists(addslashes($uploadUrl)))
			mkdir(addslashes($uploadUrl), 0777, true);
		
		$imagesPath = [];
		foreach ($this->image as $image) 
		{
			$imagesPath[] = $uploadUrl . $image->name;
			$imagine = Image::getImagine()
				->open($image->tempName)
				->thumbnail(new Box($maxSize, $maxSize))
				->save($uploadUrl . $image->name, ['quality' => $quality]);
		}
		
		return $imagesPath;
    }
}

