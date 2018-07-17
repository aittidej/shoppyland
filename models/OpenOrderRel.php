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
 * @property string $subtotal
 * @property int $qty
 *
 * @property OpenOrder $openOrder
 * @property Product $product
 */
class OpenOrderRel extends \yii\db\ActiveRecord
{
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
            [['open_order_id', 'product_id', 'qty'], 'integer'],
            [['unit_price', 'subtotal'], 'number'],
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
            'subtotal' => 'Subtotal',
            'qty' => 'Qty',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOpenOrder()
    {
        return $this->hasOne(OpenOrder::className(), ['open_order_id' => 'open_order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['product_id' => 'product_id']);
    }
}
