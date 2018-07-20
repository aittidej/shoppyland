<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserSearch represents the model behind the search form of `app\models\User`.
 */
class UserSearch extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'role_id', 'is_wholesale', 'status'], 'integer'],
            [['username', 'password', 'name', 'email', 'phone', 'address', 'last_login', 'creation_datetime', 'payment_method'], 'safe'],
            [['exchange_rate'], 'number'],
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
        $query = User::find()->with('role');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort' => [
				'defaultOrder' => ['user_id' => SORT_ASC],
				'attributes' => [
                    'user_id',
                    'name',
                    'email',
                    'role.title',
                    'username',
                    'last_login',
                    'payment_method',
                    'is_wholesale',
                    'exchange_rate',
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
            'user_id' => $this->user_id,
            'role_id' => $this->role_id,
            'last_login' => $this->last_login,
            'creation_datetime' => $this->creation_datetime,
            'is_wholesale' => $this->is_wholesale,
            'exchange_rate' => $this->exchange_rate,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 'username', $this->username])
            ->andFilterWhere(['ilike', 'password', $this->password])
            ->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'email', $this->email])
            ->andFilterWhere(['ilike', 'phone', $this->phone])
            ->andFilterWhere(['ilike', 'address', $this->address])
            ->andFilterWhere(['ilike', 'payment_method', $this->payment_method]);

        return $dataProvider;
    }
}
