<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "receipt".
 *
 * @property int $receipt_id
 * @property int $brand_id
 * @property string $creation_datetime
 * @property string $buy_date
 * @property int $msg_number
 * @property string $message_id
 * @property array $data
 * @property int $udate
 *
 * @property Brand $brand
 */
class Receipt extends DbTools
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'receipt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['brand_id', 'msg_number', 'udate'], 'default', 'value' => null],
            [['brand_id', 'msg_number', 'udate', 'unread', 'number_of_items'], 'integer'],
			[['total'], 'number'],
            [['creation_datetime', 'buy_date', 'data'], 'safe'],
            [['transaction_id'], 'string', 'max' => 100],
            [['message_id'], 'string', 'max' => 512],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brand::className(), 'targetAttribute' => ['brand_id' => 'brand_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'receipt_id' => 'Receipt ID',
            'brand_id' => 'Brand ID',
            'creation_datetime' => 'Creation Datetime',
            'buy_date' => 'Buy Date',
            'msg_number' => 'Msg Number',
            'message_id' => 'Message ID',
            'data' => 'Data',
            'udate' => 'Udate',
            'unread' => 'Unread',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id']);
    }
}
