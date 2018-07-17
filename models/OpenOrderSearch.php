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
            [['open_order_id', 'lot_number', 'user_id', 'number_of_box', 'status'], 'integer'],
            [['creation_datetime'], 'safe'],
            [['total_weight', 'total_usd', 'total_baht'], 'number'],
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
        $query = OpenOrder::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'lot_number' => $this->lot_number,
            'user_id' => $this->user_id,
            'creation_datetime' => $this->creation_datetime,
            'number_of_box' => $this->number_of_box,
            'total_weight' => $this->total_weight,
            'total_usd' => $this->total_usd,
            'total_baht' => $this->total_baht,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
