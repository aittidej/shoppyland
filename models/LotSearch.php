<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Lot;

/**
 * LotSearch represents the model behind the search form of `app\models\Lot`.
 */
class LotSearch extends Lot
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lot_id', 'lot_number', 'user_id', 'brand_id'], 'integer'],
            [['creation_datetime', 'start_date', 'end_date', 'shipped_date'], 'safe'],
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
        $query = Lot::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort' => [
				'defaultOrder' => ['lot_number' => SORT_DESC],
				'attributes' => [
                    'lot_id',
                    'lot_number',
                    'user.name',
                    'brand.title',
                    'creation_datetime',
                    'start_date',
                    'end_date',
                    'shipped_date',
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
            'lot_id' => $this->lot_id,
            'lot_number' => $this->lot_number,
            'brand_id' => $this->brand_id,
            'user_id' => $this->user_id,
            'creation_datetime' => $this->creation_datetime,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        return $dataProvider;
    }
}
