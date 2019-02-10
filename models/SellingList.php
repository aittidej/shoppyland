<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "selling_list".
 *
 * @property int $selling_list_id
 * @property int $lot_id
 * @property int $product_id
 * @property string $price
 * @property int $status
 *
 * @property Lot $lot
 * @property Product $product
 */
class SellingList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'selling_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lot_id', 'product_id', 'status'], 'default', 'value' => null],
            [['lot_id', 'product_id', 'status'], 'integer'],
            [['price'], 'number'],
			[['creation_datetime'], 'safe'],
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
            'selling_list_id' => 'Selling List ID',
            'lot_id' => 'Lot ID',
            'product_id' => 'Product ID',
            'price' => 'Price',
            'status' => 'Status',
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
