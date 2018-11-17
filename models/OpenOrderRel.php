<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "open_order_rel".
 *
 * @property int $open_order_rel_id
 * @property int $open_order_id
 * @property int $product_id
 * @property string $unit_price
 * @property int $qty
 * @property int $need_attention
 *
 * @property OpenOrder $openOrder
 * @property Product $product
 */
class OpenOrderRel extends \yii\db\ActiveRecord
{
	public $subtotal;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'open_order_rel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['open_order_id', 'product_id', 'qty'], 'default', 'value' => null],
            [['open_order_id', 'product_id', 'qty', 'need_attention', 'manually_set', 'free_labor'], 'integer'],
            [['unit_price'], 'number'],
			[['currency'], 'string'],
            [['open_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => OpenOrder::className(), 'targetAttribute' => ['open_order_id' => 'open_order_id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'product_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'open_order_rel_id' => 'Open Order Rel ID',
            'open_order_id' => 'Open Order ID',
            'product_id' => 'Product ID',
            'unit_price' => 'Unit Price',
            'qty' => 'Qty',
            'need_attention' => 'Need Attention',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOpenOrder()
    {
        return $this->hasOne(OpenOrder::className(), ['open_order_id' => 'open_order_id'])->with('user');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['product_id' => 'product_id'])->with('brand');
    }
}
