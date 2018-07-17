<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "open_order".
 *
 * @property int $open_order_id
 * @property int $lot_number
 * @property int $user_id
 * @property string $creation_datetime
 * @property int $number_of_box
 * @property string $total_weight
 * @property string $total_usd
 * @property string $total_baht
 * @property int $status
 *
 * @property User $user
 * @property OpenOrderRel[] $openOrderRels
 */
class OpenOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'open_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lot_number', 'user_id', 'number_of_box', 'status'], 'default', 'value' => null],
            [['lot_number', 'user_id', 'number_of_box', 'status'], 'integer'],
            [['creation_datetime'], 'safe'],
            [['total_weight', 'total_usd', 'total_baht'], 'number'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'open_order_id' => 'Open Order ID',
            'lot_number' => 'Lot Number',
            'user_id' => 'User ID',
            'creation_datetime' => 'Creation Datetime',
            'number_of_box' => 'Number Of Box',
            'total_weight' => 'Total Weight',
            'total_usd' => 'Total Usd',
            'total_baht' => 'Total Baht',
            'status' => 'Status',
        ];
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
    public function getOpenOrderRels()
    {
        return $this->hasMany(OpenOrderRel::className(), ['open_order_id' => 'open_order_id']);
    }
}
