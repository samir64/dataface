<?php
/**
 * Created by PhpStorm.
 * User: samir
 * Date: 1/4/17
 * Time: 5:54 PM
 */

namespace Datafase;


use Datafase\Mongo\Php5Mongo;
use Datafase\Mongo\Php7Mongo;


require_once realpath(dirname(__FILE__) . "/" . "Mongo/Php5Mongo.php");
require_once realpath(dirname(__FILE__) . "/" . "Mongo/Php7Mongo.php");


/**
 * Class Mongo
 * @package Datafase
 */
class Mongo Extends \Datafase
{
	/**
	 * @var Php5Mongo|Php7Mongo
	 */
	private $db;


	/**
	 * @param int $phpVersion
	 * Mongo constructor.
	 * @param string $host
	 * @param string $port
	 * @param string $username
	 * @param string $password
	 * @param string $dbName
	 */
	public function __construct($phpVersion, $dbName, $host = "localhost", $port = "27017", $username = "", $password = "")
	{
		switch ($phpVersion) {
			case 5:
				$this->db = new Php5Mongo($dbName, $host, $port, $username, $password);
				break;

			case 7:
				$this->db = new Php7Mongo($dbName, $host, $port, $username, $password);
				break;
		}
	}


	//TODO Public Functions: Properties Getter/Setter
	/**
	 * @param string $host
	 */
	public function setHost($host)
	{
		$this->db->setHost($host);
	}

	/**
	 * @param string $port
	 */
	public function setPort($port)
	{
		$this->db->setPort($port);
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->db->setUsername($username);
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->db->setPassword($password);
	}

	/**
	 * @param string $dbName
	 */
	public function setDbName($dbName)
	{
		$this->db->setDbName($dbName);
	}


	//TODO Public Functions: Actions
	/**
	 * @param string $collection
	 * @param array $fields
	 *
	 * @return string
	 */
	public function insert($collection, array $fields = [])
	{
		return $this->db->insert($collection, $fields);
	}

	public function select($collection, array $conditions = [], array $sort = [])
	{
		return $this->db->select($collection, $conditions, $sort);
	}

	public function selectDistinct($collection, array $conditions = [], array $fields = [], array $sort = [])
	{
		return $this->db->selectDistinct($collection, $conditions, $fields, $sort);
	}

	public function delete($collection, array $conditions = [])
	{
		return $this->db->delete($collection, $conditions);
	}

	public function update($collection, array $conditions = [], array $fields = [])
	{
		return $this->db->update($collection, $conditions, $fields);
	}

	public function count($collection)
	{
		return $this->db->count($collection);
	}

	public function query($collection, $command, $parameters)
	{
		return $this->db->query($collection, $command, $parameters);
	}
}