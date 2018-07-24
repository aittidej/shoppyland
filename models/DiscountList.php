<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "discount_list".
 *
 * @property int $discount_list_id
 * @property string $title
 * @property array $discount_json
 * @property int $status
 *
 * @property LotRel[] $lotRels
 */
class DiscountList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'discount_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['discount_json'], 'safe'],
            [['status'], 'default', 'value' => null],
            [['status'], 'integer'],
            [['title'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'discount_list_id' => 'Discount List ID',
            'title' => 'Title',
            'discount_json' => 'Discount Json',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLotRels()
    {
        return $this->hasMany(LotRel::className(), ['discount_list_id' => 'discount_list_id']);
    }
}
