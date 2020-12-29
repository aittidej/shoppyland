<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\db\Connection;
use app\models\Account;

/**
 * Database pool and factory responsible for creating and maintainig conections to any required account database.
 */
class DatabasePool extends Component
{
	public $server;
    /**
     * @var Connection[] Database conection pool
     */
	private $dbs = [];
	/**
	 * @var int Account id of the active connection
	 */
	private $_activeAccountDbName;

	/**
	 * {@inheritDoc}
	 * @see \yii\base\Object::init()
	 */
	public function init() {
	    //TODO: Verify this logic while using a command
	    
	    // Add the original connection object to the pool
	    if($dbName = $this->dbName) {
	        $db = Yii::$app->get('db');
	        
	        //if(substr($db->dsn, -1) == '_') {
	            $db->dsn = $db->dsn . $dbName;
	        //}
	        
	        $this->dbs[$dbName] = $db;
	        $this->_activeAccountDbName = $dbName;
	    }
	}
	
	/*public function getAccount() {
		return Account::findOne(['domain_name' => $_SERVER['SERVER_NAME']]);
	}
	
	public function getDbName() 
	{
		if(empty(Yii::$app->session->get('dbName')))
		{
			$dbName = $this->account->db_name;
			\Yii::$app->session->set('dbName', $dbName);
			return $dbName;
		}
		else
			return Yii::$app->session->get('dbName');
	}*/
	
	public function getDbName() 
	{
		if(empty($_SERVER['SERVER_NAME']) && !empty($_SERVER['argv'][2]))
			return $_SERVER['argv'][2];
		else if(empty($_SERVER['SERVER_NAME']))
			return "shoppyland";
			//die('No server!');
		return empty(Yii::$app->params['account'][$_SERVER['SERVER_NAME']]['db_name']) ? false : Yii::$app->params['account'][$_SERVER['SERVER_NAME']]['db_name'];
	}
	
	/**
	 * Gets the original account connection for the given session
	 * 
	 * @return Connection
	 */
	public function getSessionDb() {
        return $this->get($this->dbName);	    
	}
	
	/**
	 * Gets or creates a connection for the specified account id
	 * 
	 * @param int $accountId Account id
	 * @return Connection Connection object
	 */
	public function get($dbName) {
		if(!isset($this->dbs[$dbName])) {
			$prototypeDb = $this->getSessionDb();
			$this->dbs[$dbName] = new Connection([
				'dsn' => Yii::$app->params['database']['dsn'] . $dbName,
				'username' => $prototypeDb->username,
				'password' => $prototypeDb->password,
				'charset' => $prototypeDb->charset,
				'enableSchemaCache' => $prototypeDb->enableSchemaCache,
				'schemaCacheDuration' => $prototypeDb->schemaCacheDuration,
				'schemaCache' => $prototypeDb->schemaCache,
			]);
        }

		return $this->dbs[$dbName];
	}
    
	/**
	 * Sets the active db object to a connection to the given account's database
	 * 
	 * @param int $accountId Account id
	 * @return DatabasePool The current object
	 */
	public function setActiveAccountDb($dbName) {
		Yii::$app->set('db', $this->get($dbName));
	    $this->_activeAccountDbName = $dbName;
	    
	    return $this;
	}
	
	/**
	 * Gets the currently active connection account id
	 * 
	 * @return int
	 */
	public function getActiveAccountDbName() {
	    return $this->_activeAccountDbName;
	}
	
	/**
	 * Gets the active account connection
	 * 
	 * @return Connection The active connection
	 */
	public function getActiveDb() {
	    return $this->get($this->dbName);
	}
	
	/**
	 * Restores the active db object to the original one for the current session
	 * 
	 * @return DatabasePool The current object
	 */
	public function restoreActiveAccountDb() {
	    return $this->setActiveAccountDb($this->dbName);
	}
	
	/**
	 * Gets the connection to the hub database
	 * 
	 * @return Connection The connection object
	 */
	public function getMasterDb() {
	    return Yii::$app->get('db_master');
	}
}
