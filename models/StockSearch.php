<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Stock;

/**
 * StockSearch represents the model behind the search form of `app\models\Stock`.
 */
class StockSearch extends Stock
{
	public $test;
	
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stock_id', 'lot_id', 'product_id', 'qty', 'current_qty'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $lotNumber)
    {
        $query = Stock::find()->joinwith('lot')->with('product')->where(['lot.lot_number'=>$lotNumber]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort' => [
				'defaultOrder' => ['qty' => SORT_DESC],
				'attributes' => [
					'lot_id',
					'product_id',
					'qty',
					'current_qty',
					'product.title',
					'product.upc',
				]
			],
			'pagination' => [
				'pageSize' => 100,
			],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
		
        // grid filtering conditions
        $query->andFilterWhere([
            'stock_id' => $this->stock_id,
            'lot_id' => $this->lot_id,
            'product_id' => $this->product_id,
            'qty' => $this->qty,
            'current_qty' => $this->current_qty,
        ]);

        return $dataProvider;
    }
}
