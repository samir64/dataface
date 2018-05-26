<?php

/**
 * Created by PhpStorm.
 * User: samir
 * Date: 11/29/16
 * Time: 6:48 PM
 */


/**
 * Class Datafase
 * @package Datafase
 */
abstract class Datafase
{
	//TODO Private Variables
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


	//TODO Public Functions: Properties Getter/Setter
	/**
	 * @return string
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * @param string $host
	 */
	public function setHost($host)
	{
		$this->host = $host;
	}

	/**
	 * @return string
	 */
	public function getPort()
	{
		return $this->port;
	}

	/**
	 * @param string $port
	 */
	public function setPort($port)
	{
		$this->port = $port;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * @return string
	 */
	public function getDbName()
	{
		return $this->dbName;
	}

	/**
	 * @param string $dbName
	 */
	public function setDbName($dbName)
	{
		$this->dbName = $dbName;
	}


	//TODO Public Functions: Actions
	public abstract function insert($table, array $fields = [], $return = true);
	public abstract function select($table, array $conditions = [], array $sort = []);
	public abstract function selectDistinct($table, array $conditions = [], array $fields = [], array $sort = []);
	public abstract function delete($table, array $conditions = [], $return = true);
	public abstract function update($table, array $conditions = [], array $fields = [], $return = true);
	public abstract function count($table);
	protected abstract function reconnect();
}