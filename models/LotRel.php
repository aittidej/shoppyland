<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lot_rel".
 *
 * @property int $lot_rel_id
 * @property int $lot_id
 * @property int $product_id
 * @property int $discount_list_id
 * @property string $price
 * @property string $total
 * @property string $overwrite_total
 * @property string $creation_datetime
 * @property string $bought_date
 * @property string $currency
 *
 * @property DiscountList $discountList
 * @property Lot $lot
 * @property Product $product
 */
class LotRel extends DbTools
{
	public $subtotal;
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lot_rel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lot_id', 'product_id', 'discount_list_id'], 'default', 'value' => null],
            [['lot_id', 'product_id', 'discount_list_id'], 'integer'],
            [['price', 'overwrite_total', 'total', 'bought_price'], 'number'],
			[['currency'], 'string'],
			[['creation_datetime', 'bought_date', 'shipped_date'], 'safe'],
            [['discount_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => DiscountList::className(), 'targetAttribute' => ['discount_list_id' => 'discount_list_id']],
            [['lot_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lot::className(), 'targetAttribute' => ['lot_id' => 'lot_id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'product_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lot_rel_id' => 'Lot Rel ID',
            'lot_id' => 'Lot ID',
            'product_id' => 'Product ID',
            'discount_list_id' => 'Discount List ID',
            'price' => 'Price',
            'total' => 'Total',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscountList()
    {
        return $this->hasOne(DiscountList::className(), ['discount_list_id' => 'discount_list_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLot()
    {
        return $this->hasOne(Lot::className(), ['lot_id' => 'lot_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['product_id' => 'product_id']);
    }
	
	public function getUnitPrice()
    {
		if($this->overwrite_total)
			return $this->overwrite_total;
		
		//return Yii::$app->controller->priceDiscountCalculator($this->price, $this->discount_list_id);
		return $this->total;
	}
}
