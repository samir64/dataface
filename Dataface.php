<?php

/**
 * Created by PhpStorm.
 * User: samir
 * Date: 11/29/16
 * Time: 6:48 PM
 */


/**
 * Class Dataface
 * @package Dataface
 */
abstract class Dataface
{
	//NOTE Private Variables
	/**
	 * @var string
	 */
	protected $host;
	/**
	 * @var string
	 */
	protected $port;
	/**
	 * @var string
	 */
	protected $username;
	/**
	 * @var string
	 */
	protected $password;
	/**
	 * @var string
	 */
	protected $dbName;


	//NOTE Public Functions: Properties Getter/Setter
	public function __get($name)
	{
		switch ($name) {
			case "host":
				return $this->host;
				break;

			case "port":
				return $this->port;
				break;

			case "username":
				return $this->username;
				break;

			case "password":
				return $this->password;
				break;

			case "dbName":
				return $this->dbName;
				break;
		}
	}

	public function __set($name, $value)
	{
		switch ($name) {
			case "host":
				$this->host = $value;
				break;

			case "port":
				$this->port = $value;
				break;

			case "username":
				$this->username = $value;
				break;

			case "password":
				$this->password = $value;
				break;

			case "dbName":
				$this->dbName = $value;
				break;
		}

		$this->recconnect();
	}


	//NOTE Public Functions: Actions
	public abstract function insert($table, array $fields = [], $returnRow = false);
	public abstract function select($table, array $conditions = [], array $sort = [], $offset = 0, $limit = -1);
	public abstract function selectDistinct($table, array $conditions = [], array $fields = [], array $sort = [], $offset = 0, $limit = -1);
	public abstract function delete($table, array $conditions = []);
	public abstract function update($table, array $conditions = [], array $fields = []);
	public abstract function count($table, array $query = []);
	protected abstract function reconnect();
}