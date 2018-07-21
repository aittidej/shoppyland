<?php

namespace app\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "product".
 *
 * @property int $product_id
 * @property string $upc
 * @property string $model
 * @property int $brand_id
 * @property string $category
 * @property string $base_price
 * @property string $title
 * @property string $weight
 * @property int $status
 * @property string $description
 * @property string $color
 * @property string $size
 * @property string $dimension
 * @property array $image_path
 *
 * @property OpenOrderRel[] $openOrderRels
 * @property Brand $brand
 * @property Category $category
 */
class Product extends \yii\db\ActiveRecord
{
	public $items;
	public $image;
	public $imagPath;
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['brand_id', 'status'], 'default', 'value' => null],
            [['brand_id', 'status'], 'integer'],
            [['base_price', 'weight'], 'number'],
            [['description'], 'string'],
            [['image_path', 'json_data'], 'safe'],
            [['upc', 'model', 'color', 'size', 'dimension', 'category'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 255],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brand::className(), 'targetAttribute' => ['brand_id' => 'brand_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'Product ID',
            'upc' => 'UPC',
            'model' => 'Model',
            'brand_id' => 'Brand',
            'base_price' => 'Base Price ($)',
            'category' => 'Category',
            'title' => 'Title',
            'weight' => 'Weight',
            'status' => 'Status',
            'description' => 'Description',
            'color' => 'Color',
            'size' => 'Size',
            'dimension' => 'Dimension',
            'image_path' => 'Image Path',
        ];
    }
   
	public function beforeSave($insert) 
	{
        if (parent::beforeSave($insert)) 
		{
			$this->title = trim($this->title);
			$this->category = trim($this->category);
			$this->upc = trim($this->upc);
			$this->model = trim($this->model);
			$this->color = trim($this->color);
			$this->size = trim($this->size);
			$this->dimension = trim($this->dimension);
			if(!is_numeric($this->weight))
				$this->weight = 0;
			
            return true;
        } 

		return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOpenOrderRels()
    {
        return $this->hasMany(OpenOrderRel::className(), ['product_id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id']);
    }
	
	public function getFirstImage()
    {
		if(empty($this->image_path))
			return "http://www.topprintltd.com/global/images/PublicShop/ProductSearch/prodgr_default_300.png";
			
		$imagePath = $this->image_path;
        //return Yii::$app->request->BaseUrl . $imagePath[0];
		if (strpos($imagePath[0], 'http') !== false)
			return $imagePath[0];
		else
			return Url::base(true) .'/'. $imagePath[0]; 
    }
}
