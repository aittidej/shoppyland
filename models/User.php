<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

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
 * @property string $labor_charge_json
 * @property string $currency_base
 *
 * @property OpenOrder[] $openOrders
 * @property Role $role
 */
class User extends DbTools implements IdentityInterface
{
    public $authKey;
    public $accessToken;
    public $temp_password;
	
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
            [['last_login', 'creation_datetime', 'labor_charge_json'], 'safe'],
            [['address', 'currency_base'], 'string'],
            [['exchange_rate'], 'number'],
            [['name', 'username', 'password', 'email', 'token'], 'string', 'max' => 255],
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
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
	public static function findByUsername($username)
    {
		return static::find()->where("LOWER(username)='" . trim(strtolower($username)) . "'")->one();
    }
	
	/**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
	
	/**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) 
    {
        return Yii::$app->passwordhash->validate_password($password, $this->password);
    }
	
	/**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Security::generatePasswordHash($password);
    }
	
	
	public function getIsAdmin()
	{
		return ($this->role->title == 'Admin');
	}
	
	public function getIsClient()
	{
		return ($this->role->title == 'Client');
	}
	
	public function getIsVendor()
	{
		return ($this->role->title == 'Vendor');
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
