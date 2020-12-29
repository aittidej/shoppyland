<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stock".
 *
 * @property int $stock_id
 * @property int $lot_id
 * @property int $product_id
 * @property int $qty
 * @property int $current_qty
 *
 * @property Lot $lot
 * @property Product $product
 */
class Stock extends DbTools
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stock';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lot_id', 'product_id', 'qty', 'current_qty'], 'default', 'value' => null],
            [['lot_id', 'product_id', 'qty', 'current_qty'], 'integer'],
            [['lot_id', 'product_id'], 'unique', 'targetAttribute' => ['lot_id', 'product_id']],
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
            'stock_id' => 'Stock ID',
            'lot_id' => 'Lot ID',
            'product_id' => 'Product ID',
            'qty' => 'Qty',
            'current_qty' => 'Current Qty',
        ];
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
