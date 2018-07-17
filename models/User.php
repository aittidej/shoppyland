<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $user_id
 * @property string $name
 * @property int $status
 * @property int $role_id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $phone
 * @property string $last_login
 * @property string $creation_datetime
 * @property string $address
 * @property string $payment_method
 * @property int $is_wholesale
 * @property string $exchange_rate
 *
 * @property OpenOrder[] $openOrders
 * @property Role $role
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'role_id', 'is_wholesale'], 'default', 'value' => null],
            [['status', 'role_id', 'is_wholesale'], 'integer'],
            [['last_login', 'creation_datetime'], 'safe'],
            [['address'], 'string'],
            [['exchange_rate'], 'number'],
            [['name', 'username', 'password', 'email'], 'string', 'max' => 255],
            [['phone', 'payment_method'], 'string', 'max' => 100],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['role_id' => 'role_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'name' => 'Name',
            'status' => 'Status',
            'role_id' => 'Role ID',
            'username' => 'Username',
            'password' => 'Password',
            'email' => 'Email',
            'phone' => 'Phone',
            'last_login' => 'Last Login',
            'creation_datetime' => 'Creation Datetime',
            'address' => 'Address',
            'payment_method' => 'Payment Method',
            'is_wholesale' => 'Is Wholesale',
            'exchange_rate' => 'Exchange Rate',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOpenOrders()
    {
        return $this->hasMany(OpenOrder::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['role_id' => 'role_id']);
    }
}
