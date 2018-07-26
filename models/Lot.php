<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lot".
 *
 * @property int $lot_id
 * @property int $lot_number
 * @property string $creation_datetime
 *
 * @property LotRel[] $lotRels
 */
class Lot extends \yii\db\ActiveRecord
{
	public $items;
	public $price;
	public $discount_list_id;
	public $overwrite_total;
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lot';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['lot_number'], 'default', 'value' => null],
            [['lot_number'], 'integer'],
            [['lot_number'], 'required'],
            [['creation_datetime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lot_id' => 'Lot ID',
            'lot_number' => 'Lot Number',
            'creation_datetime' => 'Creation Datetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLotRels()
    {
        return $this->hasMany(LotRel::className(), ['lot_id' => 'lot_id']);
    }
}
