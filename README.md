# Dataface v1.2.1

Dataface is a new face of your data

## Create Database

### For MySql

```php
$db = new \Dataface\MySql("dbname", "host", "port", "username", "passoord");
```

### For MongoDB

_In PHP 5.x_

```php
$db = new \Dataface\Mongo(5, "dbname", "host", "port", "username", "passoord");
```

_In PHP 7.x_

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

### Sample Entities

**For this database:**

Table `persons`:

| Column Name | Column Type |
| ----------- | ----------- |
| _id         | CHAR(50)    |
| first_name  | CHAR(50)    |
| last_name   | CHAR(50)    |


Table `numbers`:

| Column Name  | Column Type |
| ------------ | ----------- |
| _id          | CHAR(50)    |
| person_id    | CHAR(50)    |
| phone_number | CHAR(20)    |

#### _Simple Entity_

```php
class Person extends \Dataface\Entity
{
    protected $_first_name;
    protected $_last_name;

    public function __construct($db, $id = null, $defaultFields = array())
    {
        $this->_first_name = new \Dataface\Entity\Field("first_name", "string");
        $this->_last_name = new \Dataface\Entity\Field("last_name", "string");

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
class Number extends \Dataface\Entity
{
    protected $_personId;
    protected $_phone_number;

    public function __construct($db, $id = null, $defaultFields = array())
    {
        $this->_personId = new \Dataface\Entity\Field("person_id", "string");
        $this->_phone_number = new \Dataface\Entity\Field("phone_number", "string");

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

#### _Advanced Entity_

```php
class Person extends \Dataface\Entity
{
    protected $_first_name;
    protected $_last_name;

    public function __construct($db, $id = null, $defaultFields = array())
    {
        $this->_first_name = new \Dataface\Entity\Field("first_name", "string");
        $this->_last_name = new \Dataface\Entity\Field("last_name", "string");

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
class Number extends \Dataface\Entity
{
    protected $_person;
    protected $_phone_number;

    public function __construct($db, $id = null, $defaultFields = array())
    {
        $this->_person = new \Dataface\Entity\Field("person_id", "string");
        $this->_phone_number = new \Dataface\Entity\Field("phone_number", "string");

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

### Entity functions

```php
function refresh(); // If entity's id has value and is exists in database, set other fields
function search(array $sort = []); // search data matched by entity fields (except id)
function searchDistinct(array $fields, array $sort = []);
function update(); // If entity's id has value and is exists in database, edit row with that id by entity fields, else if entity is not exists in database, insert new record by entity data
function delete();
function count(); // Returns entities like as this entity values
```