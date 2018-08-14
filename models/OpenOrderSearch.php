<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OpenOrder;

/**
 * OpenOrderSearch represents the model behind the search form of `app\models\OpenOrder`.
 */
class OpenOrderSearch extends OpenOrder
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['open_order_id', 'lot_id', 'user_id', 'number_of_box', 'status'], 'integer'],
            [['creation_datetime'], 'safe'],
            [['total_weight', 'shipping_cost', 'additional_cost'], 'number'],
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
    public function search($params)
    {
        $query = OpenOrder::find()->joinwith('user')->joinwith('lot');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort' => [
			'defaultOrder' => ['lot.lot_number' => SORT_DESC, 'user.name' => SORT_ASC],
			'attributes' => [
				'open_order_id',
				'user.name',
				'number_of_box',
				'total_weight',
				'numberOfItems',
				'creation_datetime',
				'lot.lot_number',
			]
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
            'open_order_id' => $this->open_order_id,
            'lot_id' => $this->lot_id,
            'user_id' => $this->user_id,
            'number_of_box' => $this->number_of_box,
            'total_weight' => $this->total_weight,
            'shipping_cost' => $this->shipping_cost,
            'status' => $this->status,
        ]);
		
		if(!empty($this->creation_datetime))
			$query->andFilterWhere(['=', 'DATE(creation_datetime)', date('Y-m-d', strtotime($this->creation_datetime))]);

        return $dataProvider;
    }
}
