<?php

/**
 * Created by PhpStorm.
 * User: samir
 * Date: 1/4/17
 * Time: 5:40 PM
 */

namespace Datafase;

use Datafase;
use Datafase\Entity\Field;


require_once realpath(dirname(__FILE__) . "/" . "Field.php");

/**
 * Class Entity
 * @package Datafase
 */
abstract class Entity
{
	/**
	 * @var Field
	 */
	protected $id;
	/**
	 * @var Datafase
	 */
	protected $db;
	/**
	 * @var string
	 */
	protected $tableName;


	//TODO Private Functions
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
				if ($includeId || ($field !== "id")) {
					if ($value->getValue() !== null) {
						$result[$value->getName()] = $value->getValue();
					} else if ($includeEmpties && ($value->getValue() === null)) {
						$result[$value->getName()] = $value->getDefaultValue();
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
		$vars_fields = [];

		foreach ($vars as $field => $value) {
			if ($value instanceof Field) {
				$vars_fields[$value->getName()] = $value;
			}
		}

		foreach ($fields as $field => $value) {
			if (isset($vars_fields[$field])) {
				$vars_fields[$field]->setValue($value);
			}
		}
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
				if ($value->getValueChanged()) {
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
				$value->setValueChanged($changed);
			}
		}
	}


	/**
	 * Entity constructor.
	 *
	 * @param Datafase $db
	 * @param string $tableName
	 * @param string $id
	 * @param string $idFieldName
	 * @param string $idFieldType
	 * @param array $defaultFields
	 */
	public function __construct($db, $tableName, $id = null, $idFieldName = "_id", $idFieldType = "mixed", $defaultFields = [])
	{
		$this->id = new Field($idFieldName, $idFieldType);
		$this->id->setValue($id);

		$this->db = $db;
		$this->tableName = $tableName;

		if (($defaultFields != null) && (gettype($defaultFields) === "array") && (count($defaultFields) > 0)) {
			$this->setFields($defaultFields);
		}

		$this->setEntityChanged(false);
	}


	//TODO Properties Getter/Setter
	/**
	 * @return string
	 */
	final public function getId()
	{
		return $this->id->getValue();
	}

	/**
	 * @return string
	 */
	final public function getTableName()
	{
		return $this->tableName;
	}

	/**
	 * @return Datafase
	 */
	final public function getDb()
	{
		return $this->db;
	}


	//TODO Public Functions
	/**
	 *
	 */
	final function update()
	{
		$found = (count($this->db->select($this->tableName, [$this->id->getName() => $this->id->getValue()])) > 0);

		if ($found === true) {
			if ($this->getEntityChanged() == true) {
				$this->db->update($this->tableName, [$this->id->getName() => $this->id->getValue()], $this->getFields(false, true));
			}
		} else {
			$this->db->insert($this->tableName, $this->getFields(($this->id->getValue() != null), true));
		}
	}

	/**
	 *
	 */
	final function refresh()
	{
		if ($this->id->getValue() !== null) {
			$result = $this->db->select($this->tableName, [$this->id->getName() => $this->id->getValue()]);

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
			$entities[] = new $entityType($this->db, $record[$this->id->getName()], $this->id->getName(), $this->id->getType(), $record);
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
			$entities[] = new $entityType($this->db, $record[$this->id->getName()], $this->id->getName(), $this->id->getType(), $record);
		}

		return $entities;
	}

	/**
	 *
	 */
	final function delete()
	{
		if ($this->id->getValue() !== null) {
			$this->db->delete($this->tableName, [$this->id->getName() => $this->id->getValue()]);
			$this->id->setValue(null);
		}
	}
}