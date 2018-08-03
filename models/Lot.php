<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lot".
 *
 * @property int $lot_id
 * @property int $brand_id
 * @property int $user_id
 * @property int $lot_number
 * @property string $creation_datetime
 *
 * @property LotRel[] $lotRels
 */
class Lot extends \yii\db\ActiveRecord
{
	public $items;
	public $price;
	public $discount_list_id;
	public $overwrite_total;
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lot';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lot_number', 'user_id', 'brand_id'], 'default', 'value' => null],
            [['lot_number', 'user_id', 'brand_id'], 'integer'],
            [['lot_number'], 'required'],
            [['creation_datetime'], 'safe'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'user_id']],
			[['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brand::className(), 'targetAttribute' => ['brand_id' => 'brand_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lot_id' => 'Lot ID',
            'user_id' => 'User',
            'brand_id' => 'Brand',
            'lot_id' => 'Lot ID',
            'lot_number' => 'Lot Number',
            'creation_datetime' => 'Creation Datetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLotRels()
    {
        return $this->hasMany(LotRel::className(), ['lot_id' => 'lot_id']);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id']);
    }
	
	public function getLotOwner()
    {
        return empty($this->user_id) ? 'All Buyers' : $this->user->name;
    }
	
	public function getLotOwnerText()
    {
        return empty($this->user_id) ? 'Lot #'.$this->lot_number.' (All User)' : 'Lot #'.$this->lot_number.' ('.$this->user->name.')';
    }
	
	public function getUnitPrice($productId)
	{
		$lotRel = LotRel::findOne(['lot_id' => $this->lot_id, 'product_id'=>$productId]);
		if(empty($lotRel))
			return NULL;
		
		if(empty($lotRel->overwrite_total))
			return Yii::$app->controller->priceDiscountCalculator($lotRel->price, $lotRel->discount_list_id);
		else
			return $lotRel->overwrite_total;
	}
	
}
