<?php

/**
 * Created by PhpStorm.
 * User: samir
 * Date: 11/29/16
 * Time: 6:51 PM
 */

namespace Datafase\Mongo;


require_once realpath(dirname(__FILE__) . "/" . "../Datafase.php");


class Php5Mongo extends \Datafase
{
	//TODO Private Variables
	/**
	 * @var \MongoDB\Driver\Manager
	 */
	private $db;


	//TODO Private Functions
	/**
	 *
	 */
	protected function reconnect()
	{
		$server = "";

		if ($this->username) {
			$server .= $this->username;

			if ($this->password) {
				$server .= ":" . $this->password;
			}
		}

		if ($this->host) {
			if ($server !== "") {
				$server .= "@";
			}

			$server .= $this->host;

			if ($this->port) {
				$server .= ":" . $this->port;
			}
		}

		if ($server !== "") {
			$server = "mongodb://" . $server;
		}

		$this->db = new \MongoClient($server);

		if ($this->dbName) {
//			$this->db = $db->{$this->dbName};
		} else {
//			$this->db = null;
		}
	}


	//TODO Private Functions
	/**
	 * @param \MongoCursor|\MongoDB\Driver\Cursor|\MongoDB\Driver\WriteResult $cursor
	 *
	 * @return array
	 */
	private function convertCursorToArray($cursor)
	{
		$result = [];

		foreach ($cursor as $a => $item) {
			array_push($result, (array)$item);
		}

		return $result;
	}


	//TODO Public Functions
	/**
	 * Mongo constructor.
	 * @param string $host
	 * @param string $port
	 * @param string $username
	 * @param string $password
	 * @param string $dbName
	 */
	public function __construct($dbName, $host = "localhost", $port = "27017", $username = "", $password = "")
	{
		$this->host = $host;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
		$this->dbName = $dbName;

		$this->reconnect();
	}


	//TODO Public Functions: Properties Getter/Setter
	/**
	 * @param string $host
	 */
	public function setHost($host)
	{
		parent::setHost($host);

		$this->reconnect();
	}

	/**
	 * @param string $port
	 */
	public function setPort($port)
	{
		parent::setPort($port);

		$this->reconnect();
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		parent::setUsername($username);

		$this->reconnect();
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		parent::setPassword($password);

		$this->reconnect();
	}

	/**
	 * @param string $dbName
	 */
	public function setDbName($dbName)
	{
		parent::setDbName($dbName);

		$this->reconnect();
	}


	//TODO Public Functions: Actions
	/**
	 * @param string $collection
	 * @param array $fields
	 * @param boolean $return
	 *
	 * @return array|string
	 */
	public function insert($collection, array $fields = [], $return = true)
	{
		return $this->convertCursorToArray($this->db->{$this->dbName}->{$collection}->insert($fields));
	}

	/**
	 * @param string $collection
	 * @param array $conditions
	 * @param array $sort
	 * 
	 * @return array
	 */
	public function select($collection, array $conditions = [], array $sort = [])
	{
		/** @var \MongoCursor $result */
		$result = $this->db->{$this->dbName}->{$collection}->find($conditions);

		if (is_array($sort) && (count($sort) > 0)) {
			$result = $result->sort($sort);
		}

		return $this->convertCursorToArray($result);
	}

	/**
	 * @param string $collection
	 * @param array $conditions
	 * @param array $fields
	 * @param array $sort
	 * 
	 * @return array
	 */
	public function selectDistinct($collection, array $conditions = [], array $fields = [], array $sort = [])
	{
		/** @var \MongoCursor $result */
		$result = $this->db->{$this->dbName}->{$collection}->distinct($fields, $conditions);

		if (is_array($sort) && (count($sort) > 0)) {
			$result = $result->sort($sort);
		}

		return $this->convertCursorToArray($result);
	}

	/**
	 * @param string $collection
	 * @param array $conditions
	 * @param boolean $return
	 * 
	 * @return array|boolean
	 */
	public function delete($collection, array $conditions = [], $return = true)
	{
		return $this->convertCursorToArray($this->db->{$this->dbName}->{$collection}->remove($conditions));
	}

	/**
	 * @param string $collection
	 * @param array $conditions
	 * @param array $fields
	 * @param boolean $return
	 * 
	 * @return array|boolean
	 */
	public function update($collection, array $conditions = [], array $fields = [], $return = true)
	{
		return $this->convertCursorToArray($this->db->{$this->dbName}->{$collection}->update($conditions, ['$set' => $fields]));
	}

	public function count($collection)
	{
		return (int)$this->db->{$this->dbName}->{$collection}->count();
	}

	public function query($collection, $command, $parameters)
	{
		return $this->db->{$this->dbName}->{$collection}->{$command}($parameters);
	}
}