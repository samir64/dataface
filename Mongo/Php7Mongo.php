<?php

/**
 * Created by PhpStorm.
 * User: samir
 * Date: 11/29/16
 * Time: 6:51 PM
 */

namespace Dataface\Mongo;


use MongoDB\Driver\Command;

require_once realpath(dirname(__FILE__) . "/" . "../Dataface.php");


class Php7Mongo extends \Dataface
{
    //NOTE Private Variables
    /**
     * @var \MongoDB\Driver\Manager
     */
    private $db;


    //NOTE Private Functions

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


    //NOTE Private Functions

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


    //NOTE Public Functions

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


    //NOTE Public Functions: Actions

    /**
     * @param string $collection
     * @param array $fields
     * @param boolean $return
     *
     * @return array|string
     */
    public function insert($collection, array $fields = [], $returnRow = false)
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
    public function select($collection, array $conditions = [], array $sort = [], $offset = 0, $limit = -1)
    {
        $options = [];
        if (count($sort) > 0) {
            $options["sort"] = $sort;
        }
        if ($offset > 0) {
            $options["skip"] = $offset;
        }
        if ($limit >= 0) {
            $options["limit"] = $limit;
        }

        $query = new \MongoDB\Driver\Query($conditions, $options);
        $cursor = $this->db->executeQuery($this->dbName . "." . $collection, $query);

        return $this->convertCursorToArray($cursor);
    }

    /**
     * @param string $collection
     * @param array $conditions
     * @param array $fields
     * @param array $sort
     *
     * @return array
     */
    public function selectDistinct($collection, array $conditions = [], array $fields = [], array $sort = [], $offset = 0, $limit = -1)
    {
        $options = ["distinct" => $collection, "key" => $fields, "query" => $conditions];
        if (count($sort) > 0) {
            $options["sort"] = $sort;
        }
        if ($offset > 0) {
            $options["skip"] = $offset;
        }
        if ($limit >= 0) {
            $options["limit"] = $limit;
        }

        $command = new \MongoDB\Driver\Command($options);
        $cursor = $this->convertCursorToArray($this->db->executeCommand($this->dbName, $command));

        return $this->convertCursorToArray($cursor);
    }

    /**
     * @param string $collection
     * @param array $conditions
     * @param boolean $return
     *
     * @return array|boolean
     */
    public function delete($collection, array $conditions = [])
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
    public function update($collection, array $conditions = [], array $fields = [])
    {
        $bulk = new \MongoDB\Driver\BulkWrite();
        $result = $bulk->update($conditions, ['$set' => $fields]);

        $this->db->executeBulkWrite($this->dbName . "." . $collection, $bulk);

        return $result;
    }

    public function count($collection, array $condition = [])
    {
//        $bulk = new \MongoDB\Driver\BulkWrite();
//        $result = $bulk->count();

        $params = ["count" => $collection];
        if (count($condition) > 0) {
            $params["query"] = $condition;
        }
        $command = new Command($params);

        $cursor = $this->db->executeCommand($this->dbName, $command);

        $result = $this->convertCursorToArray($cursor);

        return $result[0]["n"];
    }

    public function query($collection, $command, $parameters)
    {
        $cmd = new \MongoDB\Driver\Command($command);

        return $this->db->executeCommand($this->dbName . "." . $collection, $cmd);
    }
}