<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "promotion_code".
 *
 * @property int $promotion_code_id
 * @property string $code
 * @property string $ip_address
 * @property string $email
 * @property string $name
 * @property int $discount_amount
 * @property int $used
 */
class PromotionCode extends DbTools
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promotion_code';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['discount_amount', 'used'], 'default', 'value' => null],
            [['discount_amount', 'used'], 'integer'],
            [['code'], 'string', 'max' => 50],
            [['ip_address', 'email'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'promotion_code_id' => 'Promotion Code ID',
            'code' => 'Code',
            'ip_address' => 'Ip Address',
            'email' => 'Email',
            'name' => 'Name',
            'discount_amount' => 'Discount Amount',
            'used' => 'Used',
        ];
    }
}
