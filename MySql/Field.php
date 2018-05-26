<?php
namespace Datafase\MySql;

class Field
{
   //TODO Private Variables
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

   //TODO Constructor
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

   //TOTO Properties Getter/Setter
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        if (gettype($name) == "string") {
            $this->name = $name;
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return void
     */
    public function setType($type)
    {
        if (gettype($type) == "string") {
            $this->type = $type;
        }
    }

    /**
     * @return boolean
     */
    public function getUnsigned()
    {
        return $this->unsigned;
    }

    /**
     * @param boolean $unsigned
     *
     * @return void
     */
    public function setUnsigned($unsigned)
    {
        if (gettype($unsigned) == "boolean") {
            $this->unsigned = $unsigned;
        }
    }

    /**
     * @return boolean
     */
    public function getNotNull()
    {
        return $this->notNull;
    }

    /**
     * @param boolean $notNull
     *
     * @return void
     */
    public function setNotNull($notNull)
    {
        if (gettype($notNull) == "boolean") {
            $this->notNull = $notNull;
        }
    }

    /**
     * @return boolean
     */
    public function getAutoIncrement()
    {
        return $this->autoIncrement;
    }

    /**
     * @param boolean $autoIncrement
     *
     * @return void
     */
    public function setAutoIncrement($autoIncrement)
    {
        if (gettype($autoIncrement) == "boolean") {
            $this->autoIncrement = $autoIncrement;
        }
    }

    /**
     * @return boolean
     */
    public function getPrimary()
    {
        return $this->primary;
    }

    /**
     * @param boolean $primary
     *
     * @return void
     */
    public function setPrimary($primary)
    {
        if (gettype($primary) == "boolean") {
            $this->primary = $primary;
        }
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param boolean $default
     *
     * @return void
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }
}