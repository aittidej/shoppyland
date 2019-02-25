<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\db\Connection;

/**
 * Database pool and factory responsible for creating and maintainig conections to any required account database.
 * 
 * @author Oliver Olmos & Osmany Becerra
 */
class DatabasePool extends Component
{
    /**
     * @var Connection[] Database conection pool
     */
	private $dbs = [];
	/**
	 * @var int Account id of the active connection
	 */
	private $_activeAccountId;

	/**
	 * {@inheritDoc}
	 * @see \yii\base\Object::init()
	 */
	public function init() {
	    //TODO: Verify this logic while using a command
	    
	    // Add the original connection object to the pool
	    if($accountId = $this->getSessionAccountId()) {
	        $db = Yii::$app->get('db');
	        
	        if(substr($db->dsn, -1) == '_') {
	            $db->dsn = $db->dsn . $accountId;
	        }
	        
	        $this->dbs[$accountId] = $db;
	        $this->_activeAccountId = $accountId;
	    }
	}
	
	/**
	 * Gets the session account id
	 * 
	 * @return int Account id of the current session
	 */
	public function getSessionAccountId() {
	    $user = Yii::$app->user->getIdentity();
	    
	    if(!empty($user) && !empty($user->account)) {
	        $accountId = $user->account->account_id;
	    } else {
	        $accountId = Yii::$app->session->get('user.accountId');
	    }
	        
	    return $accountId;
	}
	
	/**
	 * Gets the original account connection for the given session
	 * 
	 * @return Connection
	 */
	public function getSessionDb() {
        return $this->get($this->getSessionAccountId());	    
	}
	
	/**
	 * Gets or creates a connection for the specified account id
	 * 
	 * @param int $accountId Account id
	 * @return Connection Connection object
	 */
	public function get($accountId) {
		if(!isset($this->dbs[$accountId])) {
			$prototypeDb = $this->getSessionDb();
			$this->dbs[$accountId] = new Connection([
				'dsn' => Yii::$app->params['database']['admin']['dsn'] . $accountId,
				'username' => $prototypeDb->username,
				'password' => $prototypeDb->password,
				'charset' => $prototypeDb->charset,
				'enableSchemaCache' => $prototypeDb->enableSchemaCache,
				'schemaCacheDuration' => $prototypeDb->schemaCacheDuration,
				'schemaCache' => $prototypeDb->schemaCache,
			]);
        }

		return $this->dbs[$accountId];
	}
    
	/**
	 * Sets the active db object to a connection to the given account's database
	 * 
	 * @param int $accountId Account id
	 * @return DatabasePool The current object
	 */
	public function setActiveAccountDb($accountId) {
	    Yii::$app->set('db', $this->get($accountId));
	    $this->_activeAccountId = $accountId;
	    
	    return $this;
	}
	
	/**
	 * Sets the active account connection
	 * 
	 * @param Connection $db
	 * 
	 * @return Connection The given connection
	 */
	public function setActiveDb($db) {
	    Yii::$app->set('db', $db);
	    
	    $parts = explode('xaccount_', $db->dsn);
	    $this->_activeAccountId = count($parts) > 1 ? $parts[1] : '';
	    
	    return $db;
	}
	
	/**
	 * Gets the currently active connection account id
	 * 
	 * @return int
	 */
	public function getActiveAccountId() {
	    return $this->_activeAccountId;
	}
	
	/**
	 * Gets the active account connection
	 * 
	 * @return Connection The active connection
	 */
	public function getActiveDb() {
	    return $this->get($this->getActiveAccountId());
	}
	
	/**
	 * Restores the active db object to the original one for the current session
	 * 
	 * @return DatabasePool The current object
	 */
	public function restoreActiveAccountDb() {
	    return $this->setActiveAccountDb($this->getSessionAccountId());
	}
	
	/**
	 * Gets the connection to the hub database
	 * 
	 * @return Connection The connection object
	 */
	public function getHubDb() {
	    return Yii::$app->get('db_master');
	}
	
	/**
	 * Gets the connection to the client_data database
	 *
	 * @return Connection The connection object
	 */
	public function getClientDataDb() {
	    return Yii::$app->get('db_client_data');
	}
}
