<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\LotRel;
use app\models\Product;

/**
 * LotRelSearch represents the model behind the search form of `app\models\LotRel`.
 */
class LotRelSearch extends LotRel
{
	public $upc;
	
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lot_rel_id', 'lot_id', 'product_id', 'discount_list_id'], 'integer'],
            [['price', 'overwrite_total', 'total', 'bought_price'], 'number'],
            [['creation_datetime', 'bought_date', 'currency'], 'safe'],
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
        $query = LotRel::find()->with('product');

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
		
		if(empty($params['LotRelSearch']['upc']))
			$query->where('0=1');
		else
		{
			$this->upc = $params['LotRelSearch']['upc'];
			$products = Product::find()->select('product_id')->where(['ilike', 'upc', trim($this->upc)])->asArray()->all();
			$list = array_map(function ($entry) { return $entry['product_id']; }, $products);
			$query->where(['IN', 'product_id', $list]);
		}
		

        // grid filtering conditions
        $query->andFilterWhere([
            'lot_rel_id' => $this->lot_rel_id,
            'lot_id' => $this->lot_id,
            'product_id' => $this->product_id,
            'discount_list_id' => $this->discount_list_id,
            'price' => $this->price,
            'overwrite_total' => $this->overwrite_total,
            'creation_datetime' => $this->creation_datetime,
            'bought_date' => $this->bought_date,
            'total' => $this->total,
            'bought_price' => $this->bought_price,
        ]);

        $query->andFilterWhere(['ilike', 'currency', $this->currency]);

        return $dataProvider;
    }
}
