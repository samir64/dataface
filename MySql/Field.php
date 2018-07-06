<?php
namespace Dataface\MySql;

class Field
{
   //NOTE Private Variables
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var boolean
     */
    private $unsigned;

    /**
     * @var boolean
     */
    private $notNull;

    /**
     * @var boolean
     */
    private $autoIncrement;

    /**
     * @var boolean
     */
    private $primary;

    /**
     * @var mixed
     */
    private $default;

   //NOTE Constructor
    /**
     * @param string $name
     * @param string $type
     * @param boolean $notNull
     * @param boolean $autoIncrement
     * @param mixed $default
     */
    public function __construct($name, $type, $unsigned = false, $notNull = false, $autoIncrement = false, $primary = false, $default = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->unsigned = $unsigned;
        $this->notNull = $notNull;
        $this->autoIncrement = $autoIncrement;
        $this->primary = $primary;
        $this->default = $default;
    }

   //NOTE Properties Getter/Setter
    public function __get($name)
    {
        switch ($name) {
            case "name":
                return $this->name;
                break;

            case "type":
                return $this->type;
                break;

            case "unsigned":
                return $this->unsigned;
                break;

            case "notNull":
                return $this->notNull;
                break;

            case "autoIncrement":
                return $this->autoIncrement;
                break;

            case "primary":
                return $this->primary;
                break;

            case "default":
                return $this->default;
                break;
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case "name":
                $this->name = $value;
                break;

            case "type":
                $this->type = $value;
                break;

            case "unsigned":
                $this->unsigned = $value;
                break;

            case "notNull":
                $this->notNull = $value;
                break;

            case "autoIncrement":
                $this->autoIncrement = $value;
                break;

            case "primary":
                $this->primary = $value;
                break;

            case "default":
                $this->default = $value;
                break;
        }
    }
}