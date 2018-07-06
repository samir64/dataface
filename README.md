# Dataface v1.2.1

Dataface is a new face of your data

## Create Database

### For MySql

```php
$db = new \Dataface\MySql("dbname", "host", "port", "username", "passoord");
```

### For MongoDB

$In PHP 5.x$

```php
$db = new \Dataface\Mongo(5, "dbname", "host", "port", "username", "passoord");
```

$In PHP 7.x$

```php
$db = new \Dataface\Mongo(7, "dbname", "host", "port", "username", "passoord");
```

## Define your own Entity

1. Define a class and extends from Entity (= table entity)
2. Define table columns as protected member (= fields)
    * The Field class's constructor is: ```Field($name, $type, $default)```
3. In constructor function set fields by Field class and at end call Entity class constructor
4. You must define two protected abstract function with names ```getColumn``` and ```setColumn``` for get and set fields
5. You can define virtual related columns in get and set column's functions

### __Simple Entity__

```php
class Person extends Entity
{
    protected $_first_name;
    protected $_last_name;

    public function __construct($db, $id = null, $defaultFields = array())
    {
        $this->_first_name = new Field("first_name", "string");
        $this->_last_name = new Field("last_name", "string");

        parent::__construct($db, "persons", "_id", "string", $id, $defaultFields);
    }

    protected function getColumn($name)
    {
        switch ($name) {
            case "firstName":
                return $this->_first_name->value;
                break;

            case "lastName":
                return $this->_last_name->value;
                break;
        }
    }

    protected function setColumn($name, $value)
    {
        switch ($name) {
            case "firstName":
                $this->_first_name->value = $value;
                break;

            case "lastName":
                $this->_last_name->value = $value;
                break;
        }
    }
}
```

```php
class Number extends Entity
{
    protected $_personId;
    protected $_phone_number;

    public function __construct($db, $id = null, $defaultFields = array())
    {
        $this->_personId = new Field("person_id", "string");
        $this->_phone_number = new Field("phone_number", "string");

        parent::__construct($db, "numbers", "_id", "string", $id, $defaultFields);
    }

    protected function getColumn($name)
    {
        switch ($name) {
            case "personId":
                return $this->_personId;
                break;

            case "phoneNumber":
                return $this->_phone_number->value;
                break;
        }
    }

    protected function setColumn($name, $value)
    {
        switch ($name) {
            case "person":
                return $this->_person->value = $value;
                break;

            case "phoneNumber":
                return $this->_phone_number->value = $value;
                break;
        }
    }
}
```

### __Advanced Entity__

```php
class Person extends Entity
{
    protected $_first_name;
    protected $_last_name;

    public function __construct($db, $id = null, $defaultFields = array())
    {
        $this->_first_name = new Field("first_name", "string");
        $this->_last_name = new Field("last_name", "string");

        parent::__construct($db, "persons", "_id", "string", $id, $defaultFields);
    }

    protected function getColumn($name)
    {
        switch ($name) {
            case "firstName":
                return $this->_first_name->value;
                break;

            case "lastName":
                return $this->_last_name->value;
                break;

            case "numbers":
                $result = new Number($this->db);
                $result->person = $this->_id->value;
                return $result->search();
        }
    }

    protected function setColumn($name, $value)
    {
        switch ($name) {
            case "firstName":
                $this->_first_name->value = $value;
                break;

            case "lastName":
                $this->_last_name->value = $value;
                break;
        }
    }
}
```

```php
class Number extends Entity
{
    protected $_person;
    protected $_phone_number;

    public function __construct($db, $id = null, $defaultFields = array())
    {
        $this->_person = new Field("person_id", "string");
        $this->_phone_number = new Field("phone_number", "string");

        parent::__construct($db, "numbers", "_id", "string", $id, $defaultFields);
    }

    protected function getColumn($name)
    {
        switch ($name) {
            case "person":
                $result = new Person($this->db, $this->_person->value);
                $result->refresh();
                return $result;
                break;

            case "phoneNumber":
                return $this->_phone_number->value;
                break;
        }
    }

    protected function setColumn($name, $value)
    {
        switch ($name) {
            case "person":
                return $this->_person->value = $value;
                break;

            case "phoneNumber":
                return $this->_phone_number->value = $value;
                break;
        }
    }
}
```

## Use Your Entity

