<?php

/**
 * Created by PhpStorm.
 * User: samir
 * Date: 1/4/17
 * Time: 5:54 PM
 */

namespace Dataface;


use Dataface\Mongo\Php5Mongo;
use Dataface\Mongo\Php7Mongo;


require_once realpath(dirname(__FILE__) . "/" . "Php5Mongo.php");
require_once realpath(dirname(__FILE__) . "/" . "Php7Mongo.php");


/**
 * Class Mongo
 * @package Dataface
 */
class Mongo extends \Dataface
{
    /**
     * @var Php5Mongo|Php7Mongo
     */
    private $db;

    protected function reconnect()
    {
        $this->db->reconnect();
    }

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


    //NOTE Public Functions: Actions

    /**
     * @param string $collection
     * @param array $fields
     *
     * @return string|array
     */
    public function insert($collection, array $fields = [], $returnRow = false)
    {
        $id = $this->db->insert($collection, $fields);

        if ($returnRow === true) {
            return $this->db->select($collection, ["_id" => $id]);
        } else {
            return (string)$id;
        }
    }

    public function select($collection, array $conditions = [], array $sort = [], $offset = 0, $limit = -1)
    {
        return $this->db->select($collection, $conditions, $sort, $offset, $limit);
    }

    public function selectDistinct($collection, array $conditions = [], array $fields = [], array $sort = [], $offset = 0, $limit = -1)
    {
        return $this->db->selectDistinct($collection, $conditions, $fields, $sort, $offset, $limit);
    }

    public function delete($collection, array $conditions = [])
    {
        return $this->db->delete($collection, $conditions);
    }

    public function update($collection, array $conditions = [], array $fields = [])
    {
        return $this->db->update($collection, $conditions, $fields);
    }

    public function count($collection, array $condition = [])
    {
        return $this->db->count($collection, $condition);
    }

    public function query($collection, $command, $parameters)
    {
        return $this->db->query($collection, $command, $parameters);
    }
}