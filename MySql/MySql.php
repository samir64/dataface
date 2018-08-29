<?php

namespace Dataface;

require_once realpath(dirname(__FILE__) . "/" . "../Dataface.php");
require_once realpath(dirname(__FILE__) . "/" . "Field.php");

use \Dataface\MySql\Field;

class MySql extends \Dataface
{
    //NOTE Constants
    const MySqlEnum_AutoCreateTable = 1;
    const MySqlEnum_AutoAddField = 2;
    const MySqlEnum_AutoRemoveField = 4;
    const MySqlEnum_AutoChangeField = 8;

    //NOTE Private Variables
    /**
     * @var \mysqli
     */
    private $db;

    /**
     * @var boolean
     */
    private $auto_MySqlEnum;

    //NOTE Private Functions

    /**
     *
     */
    protected function reconnect()
    {
        $this->db = new \mysqli($this->host, $this->username, $this->password, "", $this->port);

        if ($this->dbName) {
            if (!$this->db->select_db($this->dbName) && ($this->autoCreate === true)) {
                $this->createDatabase($this->dbName);
            }
        }
    }

    /**
     * @param \mysqli_result $cursor
     *
     * @return array
     */
    private function convertCursorToArray($cursor)
    {
        $result = [];

        if ($cursor->num_rows > 0) {
            $fields = $cursor->fetch_fields();

            while ($row = $cursor->fetch_array()) {
                $item = [];
                foreach ($fields as $field) {
                    $item[$field->name] = $row[$field->name];
                }

                array_push($result, $item);
            }
        }

        return $result;
    }

    /**
     * @param string $table
     * @param array $conditions
     * @param boolean $returnCount
     * @param array $sort
     * @param boolean $distinct
     * @param array $showfields
     *
     * @return string
     */
    private function selectQuery($table, array $conditions, $returnCount = false, $distinct = false, array $showfields = [], array $sort = [], $offset = 0, $limit = -1)
    {
        /** @var \mysql_result $result */
        $conditionString = "";
        $sortString = "";
        $query = "SELECT";
        $selectedFields = "";
        if ($distinct) {
            $query .= " DISTINCT ";
            if (count($showfields) > 0) {
                foreach ($showfields as $fieldName) {
                    if ($selectedFields != "") {
                        $selectedFields .= ", ";
                    }
                    $selectedFields .= $fieldName;
                }
            }
        }

        if ($selectedFields == "") {
            $selectedFields = "*";
        }

        if ($returnCount) {
            $selectedFields = "COUNT($selectedFields) AS cnt";
        }

        $query .= " $selectedFields FROM $table";

        if (count($conditions) > 0) {
            $query .= " WHERE";

            foreach ($conditions as $field => $value) {
                if ($conditionString != "") {
                    $conditionString .= " AND";
                }
                if (gettype($value) == "string") {
                    $text = "'$value'";
                } else {
                    $text = $value;
                }
                $conditionString .= " $field = $text";
            }
        }

        $query .= $conditionString;

        if (count($sort) > 0) {
            $query .= " ORDER BY ";

            foreach ($sort as $field => $type) {
                if ($sortString != "") {
                    $sortString .= ", ";
                }
                $sortString .= "$field";
                if ($type === -1) {
                    $sortString .= "DESC";
                }
            }
        }

        $query .= "$sortString";


        if (!$returnCount) {
            $query .= " LIMIT ";

            if ($limit < 0) {
                $limit = 18446744073709551615;
            }

            if ($offset > 0) {
                $query .= "$offset, ";
            }

            $query .= "$limit";
        }
        $query .= ";";

        return $query;

        /*        $result = $this->db->query($query);
                if ($result) {
                    return $this->convertCursorToArray($result);
                } else {
                    return [];
                }*/
    }

    //NOTE Constructor

    /**
     * @param string $host
     * @param string $port
     * @param string $username
     * @param string $password
     * @param string $dbName
     * @param int $auto_MySqlEnum
     */
    public function __construct($dbName, $host = "localhost", $port = "3306", $username = "root", $password = "", $auto_MySqlEnum = 15)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->dbName = $dbName;

        $this->auto_MySqlEnum = $auto_MySqlEnum;

        $this->reconnect();
    }

    //NOTE Properties Getter/Setter

    /**
     * @return boolean
     */
    public function getAuto()
    {
        return $this->auto;
    }

    /**
     * @param boolean $auto
     *
     * @return void
     */
    public function setAuto($auto)
    {
        if (gettype($auto) === "boolean") {
            $this->auto = $auto;
        }
    }

    //NOTE Public Functions

    /**
     * @param string $table
     * @param array $fields
     * @param boolean $return
     *
     * @return array|string|int
     */
    public function insert($table, array $fields = [], $returnRow = false)
    {
        $query = "";
        $fieldsList = "";
        $valuesList = "";
        $tableId = "";
        $hasTable = true;

        if ($this->auto_MySqlEnum > 0) {
            $hasTable = ($this->db->query("SHOW TABLES LIKE '$table';")->num_rows > 0);
        }

        if ($this->auto_MySqlEnum & MySql::MySqlEnum_AutoCreateTable) {
            if (!$hasTable) {
                $tableFields = [];

                foreach ($fields as $field => $value) {
                    $type = "";
                    switch (gettype($value)) {
                        case "string":
                            $type = "VARCHAR(255)";
                            break;

                        case "int":
                        case "integer":
                            $type = "INT";
                            break;

                        case "boolean":
                        case "bool":
                            $type = "BOOL";
                            break;

                        default:
                            $type = "";
                    }
                    $isUsNnAiPk = (($field == "id") || ($field == "_id"));
                    if ($isUsNnAiPk) {
                        $tableId = $field;
                    }
                    array_push($tableFields, new Field($field, $type, $isUsNnAiPk, $isUsNnAiPk, $isUsNnAiPk, $isUsNnAiPk));
                }

                if ($tableId == "") {
                    array_push($tableFields, new Field("_id", "INT", true, true, true, true));
                }

                $this->createTable($table, $tableFields);
            }
        }

        if ($this->auto_MySqlEnum & (MySql::MySqlEnum_AutoAddField | MySql::MySqlEnum_AutoRemoveField | MySql::MySqlEnum_AutoChangeField)) {
            if ($hasTable) {
                //TODO Update table fields automate by new insert fields
            }
        }

        foreach ($fields as $fieldName => $value) {
            if ($fieldsList !== "") {
                $fieldsList .= ", ";
            }
            $fieldsList .= $fieldName;

            if ($valuesList !== "") {
                $valuesList .= ", ";
            }
            if (gettype($value) == "string") {
                $valuesList .= "'$value'";
            } else {
                $valuesList .= $value;
            }
        }

        $query = "INSERT INTO $table ($fieldsList) VALUES ($valuesList);";
        $result = $this->db->query($query);
        if ($result && ($returnRow === true)) {
            if ($tableId == "") {
                $query = "SHOW KEYS FROM $table WHERE key_name = 'PRIMARY';";
                $result = $this->db->query($query)->fetch_assoc();
                $tableId = $result["Column_name"];
            }

            return $this->select($table, [$tableId => $this->db->insert_id])[0];
        } else {
            return $this->db->insert_id;
        }
    }

    /**
     * @param string $table
     * @param array $conditions
     * @param array $sort
     *
     * @return array
     */
    public function select($table, array $conditions = [], array $sort = [], $offset = 0, $limit = -1)
    {
//        return $this->selectQuery($table, $conditions, $sort, false, [], $offset, $limit);
        $result = $this->db->query($this->selectQuery($table, $conditions, false, false, [], $sort, $offset, $limit));
        if ($result) {
            return $this->convertCursorToArray($result);
        } else {
            return [];
        }
    }

    /**
     * @param string $table
     * @param array $conditions
     * @param array $fields
     * @param array $sort
     *
     * @return array
     */
    public function selectDistinct($table, array $conditions = [], array $fields = [], array $sort = [], $offset = 0, $limit = -1)
    {
//        return $this->selectQuery($table, $conditions, $sort, true, $offset, $limit, $fields);
        $result = $this->db->query($this->selectQuery($table, $conditions, false, true, $fields, $sort, $offset, $limit));
        if ($result) {
            return $this->convertCursorToArray($result);
        } else {
            return [];
        }
    }

    /**
     * Delete Query Function
     *
     * @param string $table
     * @param array $conditions
     * @param boolean $return
     *
     * @return array|boolean
     */
    public function delete($table, array $conditions = [])
    {
        $query = "DELETE FROM $table";

        if (count($conditions) > 0) {
            $query .= " WHERE";
            $conditionString = "";

            foreach ($conditions as $fieldName => $value) {
                if ($conditionString !== "") {
                    $conditionString .= " AND";
                }

                if (gettype($value) == "string") {
                    $text = "'$value'";
                } else {
                    $text = $value;
                }

                $conditionString .= " $fieldName = $text";
            }
        }

        $query .= $conditionString;
        $result = $this->db->query($query);
        return $result;
    }

    /**
     * @param string $table
     * @param array $conditions
     * @param array $fields
     * @param boolean $return
     *
     * @return array|boolean
     */
    public function update($table, array $conditions = [], array $fields = [])
    {
        $query = "UPDATE $table";
        $setString = "";
        $conditionString = "";

        foreach ($fields as $fieldName => $value) {
            if ($setString !== "") {
                $setString .= ", ";
            }

            if (gettype($value) == "string") {
                $valueText = "'$value'";
            } else {
                $valueText = $value;
            }

            $setString .= "$fieldName = $valueText";
        }

        $query .= " SET $setString";

        if (count($conditions) > 0) {
            $query .= " WHERE";
            foreach ($conditions as $field => $value) {
                if ($conditionString !== "") {
                    $conditionString .= " AND";
                }

                if (gettype($value) == "string") {
                    $valueText = "'$value'";
                } else {
                    $valueText = $value;
                }

                $conditionString .= " $field = $valueText";
            }

            $query .= "$conditionString";
        }

        $result = $this->db->query($query);
        return $result;
    }

    /**
     * @param string $table
     * @param array $query
     *
     * @return int
     */
    public function count($table, array $query = [])
    {
        $result = $this->db->query($this->selectQuery($table, $query, true));

        if ($result) {
            $result = $this->convertCursorToArray($result);

            return (int)$result[0]["cnt"];
        } else {
            return 0;
        }
    }

    public function query($query)
    {
        return $this->db->query($query);
        #return $this->db->{$this->dbName}->{$table}->{$command}($parameters);
    }

    /**
     * @param string $db
     *
     * @return boolean
     */
    public function createDataface($db, $selectDb = true)
    {
        $query = "CREATE SCHEMA $db;";
        $result = $this->db->query($query);

        if ($result && ($selectDb === true)) {
            $this->db->select_db($db);
        }

        return $result;
    }

    /**
     * @param string $table
     * @param Field[] $fields
     *
     * @return void
     */
    public function createTable($table, array $fields)
    {
        $query = "CREATE TABLE $table (";
        $fieldsString = "";

        foreach ($fields as $field) {
            if ($field instanceof Field) {
                if ($fieldsString != "") {
                    $fieldsString .= ", ";
                }

                $fieldsString .= "{$field->name} {$field->type}";

                if ((strtolower($field->type) === "int") && ($field->unsigned === true)) {
                    $fieldsString .= " UNSIGNED";
                }

                if ($field->notNull === true) {
                    $fieldsString .= " NOT NULL";
                }

                if ((strtolower($field->type) === "int") && ($field->autoIncrement === true)) {
                    $fieldsString .= " AUTO_INCREMENT";
                }

                if ($field->primary === true) {
                    $fieldsString .= " PRIMARY KEY";
                }

                if ($field->default != null) {
                    $fieldsString .= " {$field->default}";
                }
            }
        }
        $query .= " $fieldsString);";

        $this->db->query($query);
    }

    /**
     * @param string $table
     * @param Field[] $fields
     * @param int $auto
     *
     * @return boolean
     */
    public function alterTable($table, array $fields, $auto_MySqlEnum)
    {
        //TODO Alter Table Query
    }
}
