<?php

namespace app\models;

use Yii;
use yii\db\Connection;

abstract class DbTools extends \yii\db\ActiveRecord
{
	public static $db;

	/**
	 * (For backward compatibility)
	 * 
	 * @return Connection
	 */
	public static function getDb() {
	    return Yii::$app->dbPool->getActiveDb(); // This method ensures preperly initialization of connection object
	}
}
