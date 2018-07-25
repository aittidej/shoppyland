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
 *
 * @property DiscountList $discountList
 * @property Lot $lot
 * @property Product $product
 */
class LotRel extends \yii\db\ActiveRecord
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
            [['price'], 'number'],
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
}
