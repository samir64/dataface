<?php

/**
 * Created by PhpStorm.
 * User: samir
 * Date: 1/4/17
 * Time: 5:40 PM
 */

namespace Dataface;

use Dataface;
use Dataface\Entity\Field;


require_once realpath(dirname(__FILE__) . "/" . "Field.php");

/**
 * Class Entity
 * @package Dataface
 *
 * @property-read mixed $id
 */
abstract class Entity
{
    /**
     * @var Field
     */
    protected $_id;
    /**
     * @var Dataface
     */
    protected $db;
    /**
     * @var string
     */
    protected $tableName;


    protected abstract function getColumn($name);

    protected abstract function setColumn($name, $value);


    //NOTE Private Functions

    /**
     * @param bool $includeId
     * @param bool $includeEmpties
     *
     * @return array
     */
    private function getFields($includeId = false, $includeEmpties = false)
    {
        $result = [];

        $fields = get_object_vars($this);

        /**
         * @var Field $value
         */
        foreach ($fields as $field => $value) {
            if ($value instanceof Field) {
                if ($includeId || ($field !== $this->_id->name)) {
                    if ($value->value !== null) {
                        $result[$value->name] = $value->value;
                    } else if ($includeEmpties && ($value->value === null)) {
                        $result[$value->name] = $value->defaultValue;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param $fields
     */
    private function setFields($fields)
    {
        $vars = get_object_vars($this);
        /** @var Field[] $vars_fields */
//        $vars_fields = [];

        foreach ($vars as $field => $value) {
            if ($value instanceof Field) {
//                $vars_fields[$value->name] = $value;
                if (isset($fields[$value->name])) {
                    $value->value = $fields[$value->name];
                }
            }
        }

/*        foreach ($fields as $field => $value) {
            if (isset($vars_fields[$field])) {
                $vars_fields[$field]->value = $value;
            }
        }*/
    }

    /**
     * @return bool
     */
    private function getEntityChanged()
    {
        $result = false;

        $fields = get_object_vars($this);

        /**
         * @var Field $value
         */
        foreach ($fields as $value) {
            if ($value instanceof Field) {
                if ($value->valueChanged) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * @param bool $changed
     */
    private function setEntityChanged($changed)
    {
        $result = false;

        $fields = get_object_vars($this);

        /**
         * @var Field $value
         */
        foreach ($fields as $value) {
            if ($value instanceof Field) {
                $value->valueChanged = $changed;
            }
        }
    }


    /**
     * Entity constructor.
     *
     * @param Dataface $db
     * @param string $tableName
     * @param string $idFieldName = "_id"
     * @param string $idFieldType
     * @param string $id = null
     * @param array $defaultFields
     */
    public function __construct($db, $tableName, $idFieldName, $idFieldType, $id = null, $defaultFields = [])
    {
        $this->_id = new Field($idFieldName, $idFieldType);
        $this->_id->value = $id;

        $this->db = $db;
        $this->tableName = $tableName;

        if (($defaultFields != null) && (gettype($defaultFields) === "array") && (count($defaultFields) > 0)) {
            $this->setFields($defaultFields);
        }

        $this->setEntityChanged(false);
    }


    //NOTE Properties Getter/Setter
    final public function __get($name)
    {
        switch ($name) {
            case "id":
                return $this->_id->value;
                break;

            case "tableName":
                return $this->tableName;
                break;

            case "db":
                return $this->db;
                break;
        }

        return $this->getColumn($name);
    }

    final public function __set($name, $value)
    {
        $this->setColumn($name, $value);
    }


    //NOTE Public Functions

    /**
     *
     */
    final function update()
    {
        $found = (count($this->db->select($this->tableName, [$this->_id->name => $this->_id->value])) > 0);

        if ($found === true) {
            if ($this->getEntityChanged() == true) {
                $this->db->update($this->tableName, [$this->_id->name => $this->_id->value], $this->getFields(false, false));
            }
        } else {
            $result = $this->db->insert($this->tableName, $this->getFields(($this->_id->value != null), true), false);
            $this->_id->value = $result;
        }
    }

    /**
     *
     */
    final function refresh()
    {
        if ($this->_id->value !== null) {
            $result = $this->db->select($this->tableName, [$this->_id->name => $this->_id->value]);

            if (count($result) > 0) {
                $this->setFields($result[0]);
                $this->setEntityChanged(false);
            }
        }
    }

    /**
     * @param array $sort
     *
     * @return Entity[]
     */
    final function search(array $sort = [])
    {
        $entityType = get_class($this);
        $entities = [];

        $result = $this->db->select($this->tableName, $this->getFields(), $sort);

        foreach ($result as $record) {
            $entities[] = new $entityType($this->db, $record[$this->_id->name], $record);
        }

        return $entities;
    }

    /**
     * @param array $fields
     * @param array $sort
     *
     * @return Entity[]
     */
    final function searchDistinct(array $fields, array $sort = [])
    {
        $entityType = get_class($this);
        $entities = [];

        $result = $this->db->selectDistinct($this->tableName, $this->getFields(), $fields, $sort);

        foreach ($result as $record) {
            $entities[] = new $entityType($this->db, $record[$this->_id->name], $this->_id->name, $this->_id->type, $record);
        }

        return $entities;
    }

    /**
     *
     */
    final function delete()
    {
        if ($this->_id->value !== null) {
            $this->db->delete($this->tableName, [$this->_id->name => $this->_id->value]);
            $this->_id->value = null;
        }
    }
}