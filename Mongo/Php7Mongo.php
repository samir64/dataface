<?php
/**
 * Created by PhpStorm.
 * User: samir
 * Date: 11/29/16
 * Time: 6:51 PM
 */

namespace Datafase\Mongo;


require_once realpath(dirname(__FILE__) . "/" . "../Datafase.php");


class Php7Mongo extends \Datafase
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

		$this->db = new \MongoDB\Driver\Manager($server);
	}


	//TODO Private Functions
	/**
	 * param \MongoCursor $cursor
	 * @param \MongoDB\Driver\Cursor|\MongoDB\Driver\WriteResult $cursor
	 *
	 * @return array
	 */
	private function convertCursorToArray($cursor)
	{
		$result = [];

		foreach ($cursor as $item) {
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
		$bulk = new \MongoDB\Driver\BulkWrite();
		$result = $bulk->insert($fields);

		$this->db->executeBulkWrite($this->dbName . "." . $collection, $bulk);
		return $result;
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
		$query = new \MongoDB\Driver\Query($conditions);

		return $this->convertCursorToArray($this->db->executeQuery($this->dbName . "." . $collection, $query));
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
		$command = new \MongoDB\Driver\Command(["distinct" => $collection, "key" => $fields, "query" => $conditions]);

		return $this->convertCursorToArray($this->db->executeCommand($this->dbName, $command));
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
		$bulk = new \MongoDB\Driver\BulkWrite();
		$bulk->delete($conditions);

		$this->db->executeBulkWrite($this->dbName . "." . $collection, $bulk);
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
		$bulk = new \MongoDB\Driver\BulkWrite();
		$result = $bulk->update($conditions, $fields);

		$this->db->executeBulkWrite($this->dbName . "." . $collection, $bulk);

		return $result;
	}

	public function count($collection)
	{
		$bulk = new \MongoDB\Driver\BulkWrite();
		$result = $bulk->count();

		return $result;
	}

	public function query($collection, $command, $parameters)
	{
		$cmd = new \MongoDB\Driver\Command($command);

		return $this->db->executeCommand($this->dbName . "." . $collection, $cmd);
	}
}