<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $product_id
 * @property string $upc
 * @property string $model
 * @property int $brand_id
 * @property string $base_price
 * @property int $category_id
 * @property string $title
 * @property string $weight
 * @property string $image_path
 * @property int $status
 *
 * @property OpenOrderRel[] $openOrderRels
 * @property Brand $brand
 * @property Category $category
 */
class Product extends \yii\db\ActiveRecord
{
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
            [['brand_id', 'category_id', 'status'], 'default', 'value' => null],
            [['brand_id', 'category_id', 'status'], 'integer'],
            [['base_price', 'weight'], 'number'],
            [['upc', 'model'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 255],
            [['image_path'], 'string', 'max' => 512],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brand::className(), 'targetAttribute' => ['brand_id' => 'brand_id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'category_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'Product ID',
            'upc' => 'Upc',
            'model' => 'Model',
            'brand_id' => 'Brand ID',
            'base_price' => 'Base Price',
            'category_id' => 'Category ID',
            'title' => 'Title',
            'weight' => 'Weight',
            'image_path' => 'Image Path',
            'status' => 'Status',
        ];
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['category_id' => 'category_id']);
    }
}
