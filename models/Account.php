<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "account".
 *
 * @property int $account_id
 * @property string $name
 * @property string $db_name
 * @property string $auth_key
 * @property string $domain_name
 * @property string $join_datetime
 * @property int $status
 */
class Account extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_master');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['join_datetime'], 'safe'],
            [['status'], 'default', 'value' => null],
            [['status'], 'integer'],
            [['name', 'auth_key'], 'string', 'max' => 255],
            [['db_name'], 'string', 'max' => 50],
            [['domain_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'account_id' => 'Account ID',
            'name' => 'Name',
            'db_name' => 'Db Name',
            'auth_key' => 'Auth Key',
            'domain_name' => 'Domain Name',
            'join_datetime' => 'Join Datetime',
            'status' => 'Status',
        ];
    }
}
