<?php
/**
 * Created by PhpStorm.
 * User: samir
 * Date: 1/5/17
 * Time: 6:47 PM
 */

namespace Datafase\Entity;


	/**
	 * Class Field
	 * @package Datafase\Entity
	 */
/**
 * Class Field
 * @package Datafase\Entity
 */
class Field
{
	//TODO Private Variables
	/**
	 * @var string
	 */
	private $type;
	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var mixed
	 */
	private $value;
	/**
	 * @var mixed
	 */
	private $defaultValue;
	/**
	 * @var bool
	 */
	private $valueChanged;


	/**
	 * Field constructor.
	 * @param string $name
	 * @param string $type
	 * @param mixed $defaultValue
	 */
	public function __construct($name, $type, $defaultValue = null)
	{
		$this->type = $type;
		$this->name = $name;

		$this->defaultValue = $defaultValue;
		$this->setValue($defaultValue);
		$this->valueChanged = false;
	}


	//TODO Properties Getter/Setter
	/**
	 * @return mixed
	 */
	final public function getDefaultValue()
	{
		return $this->defaultValue;
	}

	/**
	 * @return string
	 */
	final public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	final public function getName()
	{
		return (string)$this->name;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param mixed $value
	 */
	public function setValue($value)
	{
		if ($value !== null) {
			switch ($this->type) {
				case 'string':
					$value = strval($value);
					break;
				
				case 'boolean':
				case 'bool':
					$value = ($value === true);
					break;

				case 'integer':
				case 'int':
					$value = intval($value);
					break;
				
				default:
					break;
			}
		}

		if (($value == null) || ($this->type === "mixed") || (gettype($value) === $this->type)) {
			if ($value !== $this->value) {
				$this->valueChanged = true;
			}

			$this->value = $value;
		}
	}

	/**
	 * @return boolean
	 */
	final public function getValueChanged()
	{
		return $this->valueChanged;
	}

	/**
	 * @param boolean $valueChanged
	 */
	final public function setValueChanged($valueChanged)
	{
		$this->valueChanged = $valueChanged;
	}
}