<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Product;

/**
 * ProductSearch represents the model behind the search form of `app\models\Product`.
 */
class ProductSearch extends Product
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'brand_id', 'status'], 'integer'],
            [['upc', 'model', 'title', 'description', 'color', 'size', 'dimension', 'image_path', 'category', 'json_data', 'creation_datetime', 'modify_datetime'], 'safe'],
            [['base_price', 'weight'], 'number'],
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
        $query = Product::find()->where(['status'=>1]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort' => [
				'defaultOrder' => ['modify_datetime' => SORT_DESC],
				'attributes' => [
                    'product_id',
                    'brand.title',
                    'upc',
                    'model',
                    'title',
                    'category',
                    'color',
                    'size',
                    'dimension',
                    'base_price',
                    'weight',
                    'modify_datetime',
                    'creation_datetime',
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
            'product_id' => $this->product_id,
			'brand_id' => $this->brand_id,
            'base_price' => $this->base_price,
            'weight' => $this->weight,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 'upc', trim($this->upc)])
            ->andFilterWhere(['ilike', 'model', trim($this->model)])
            ->andFilterWhere(['ilike', 'title', trim($this->title)])
            ->andFilterWhere(['ilike', 'description', $this->description])
            ->andFilterWhere(['ilike', 'category', $this->category])
            ->andFilterWhere(['ilike', 'color', $this->color])
            ->andFilterWhere(['ilike', 'size', $this->size])
            ->andFilterWhere(['ilike', 'dimension', $this->dimension])
            //->andFilterWhere(['ilike', 'image_path', $this->image_path])
            ->andFilterWhere(['ilike', 'json_data', $this->json_data]);

        return $dataProvider;
    }
}
