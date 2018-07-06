<?php

/**
 * Created by PhpStorm.
 * User: samir
 * Date: 1/5/17
 * Time: 6:47 PM
 */

namespace Dataface\Entity;


/**
 * Class Field
 * @package Dataface\Entity
 */
/**
 * Class Field
 * @package Dataface\Entity
 */
class Field
{
	//NOTE Private Variables
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
		$this->__set("value", $defaultValue);
		$this->valueChanged = false;
	}


	//NOTE Properties Getter/Setter
	public function __get($name)
	{
		switch ($name) {
			case "default":
				return $this->defaultValue;
				break;

			case "type":
				return $this->type;
				break;

			case "name":
				return $this->name;
				break;

			case "value":
				return $this->value;
				break;

			case "valueChanged":
				return $this->valueChanged;
				break;
		}
	}

	public function __set($name, $value)
	{
		switch ($name) {
			case "default":
				$this->defaultValue = $value;
				break;

			case "type":
				$this->type = $value;
				break;

			case "name":
				$this->name = $value;
				break;

			case "value":
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
				break;

			case "valueChanged":
				$this->valueChanged = $value;
				break;
		}
	}
}