### Welcome to the Sandia MySQLi Database Wrapper 

# What is this?
![Sandia MySQLi](https://raw.github.com/inventtoo/SandiaMySQLi/master/design/SandiaMySQLi_mini.png "Logo Sandia MySQLi") *Sandia MySQLi* is a wrapper for MySQL Databases written in PHP to make simple some of the more recurrent tasks in databases management.

*Sandia MySQLi Database Wrapper* is maintained by Marco Fernandez member of the team [inventtoo.com](http://inventtoo.com), please feel free to visite us and send your comments to us.

This project have a MIT License, so you can modify it, redistribute it, print it, burn it, or whatever you want.

# What make Sandia MySQL better?
It's just lighter and smaller than the most libraries. And, who the hell don't love the watermelons?

# Configuration

## How to setup Sandia MySQL

```php
<?php
require_once 'Sandia_MySQLi_Wrapper.php';
$db = new SandiaMySQL();
$db->open("localhost","user","password","database");
```

# Documentation

## Strings escape Functions / \`Fields\` and 'Values' formatting Functions

Sandia MySQL have some public methods to formatting queries. These ones are used inside the class but also can be used to formatting a query outside.

* **escape_string(string)** - alias from MYSQLI::real_escape_string
* **quote_field(string)** - quote a string with backquote, ex: \`string\`
* **quote_value(string)** - quote a string with single quote, ex: 'string'
* **quote_escaped_field(string)** - quote a string with backquote before escaped it
* **quote_escaped_value(string)** - quote a string before escaped it
* **quote_fields(string)** - quote an array of strings with backquote, ex: \`string\`,\`string\`,\`string\`
* **quote_values(string)** - quote an array of strings with single quote, ex: 'string','string','string'
* **quote_escaped_fields(string)** - quote an array of strings with backquote before escaped it
* **quote_escaped_values(string)** - quote an array of strings with single quote before escaped it
 
## Query Parsing Functions

The Sandia MySQL *String ecape and String formatting functions* are used to give format to fields and values previous its use in a query. The *Query Parsing Functions* are used to formatting the query itself.

* **quote_parameters(array('field'=>'value', 'field'=>'value')** - quote an array of \`field\` = 'value'
* **parse_query(string)** - parsing a query quoting ? ('') or ignoring quote #
* **parse_where(array('field'=>'value'), operators)** - quote where with the passed operators (AND as default). NULL is prepared to ignore quoting, you can also ignore quoting usin (&), ex: &string&.

### Examples

#### parse_query()
```php
<?php
$sql = 'SELECT email FROM users WHERE name=? AND user_id=# OR username=?';
$q = $db->parse_query($sql, array('marco',13));
print_r($q);
//SELECT email FROM users WHERE name='marco' AND user_id=13
```

#### parse_where()
```php
<?php
$q = $db->parse_where(array("dinasour"=>"t-rex","superhero"=>"batman"));
print_r($q);
//AND as default operator, OUTPUT:
//WHERE `dinasour` = 't-rex' AND `superhero` = 'batman' 

$q = $db->parse_where(array("dinasour"=>"t-rex","superhero"=>"batman","city"=>"NULL","id_vehicle"=>"&25&","lake"=>"like %bravo%"),array("AND","OR"));
print_r($q);
//AND as default operator if the operators array isn't of the same size, OUTPUT:
//WHERE `dinasour` = 't-rex' AND `superhero` = 'batman' OR `city` = NULL AND `id_vehicle` = 25 AND `lake` like '%bravo%'

$q = $db->parse_where(array("dinasour"=>"t-rex","city"=>"NULL","id_vehicle"=>"> &25&"),"OR");
print_r($q);
//If the operator is a string is repeated in all the where query.
//WHERE `dinasour` = 't-rex' OR `city` = NULL OR `id_vehicle` > 25
```

#### quote_parameters()
```php
<?php
$q = $db->quote_parameters(array("name"=>"Marco","lastname"=>"Fernandez","age"=>23));
print_r($q);
//Use the internal methods quote_fields & quote_escaped_values to create an array of quoted parameters
//Array ( 
//[0] => `name` = 'Marco' 
//[1] => `lastname` = 'Fernandez' 
//[2] => `age` = '23' 
//)
```

## Execution Operations Functions

The execution queries realize two functions: parsing and execute the query. 

* **execute (sql,parameters array)** - run a query in database, and parsing if a parameters array is given.
* **multi_execute(sql, parameters array)** - run multiple queries in database, and parsing if a parameters array is given.

###Examples

#### execute()
```php
<?php
$q = $db->execute('SELECT * FROM test');
print_r($q);
// mysqli_result Object ( [current_field] => 0 [field_count] => 3 [lengths] => [num_rows] => 1 [type] => 0 ) 

$q = $db->fetch();
print_r($q);
// Array ( [0] => Array ( [field] => field 001 [value] => value 001 [status] => 1 ) )
````

## Log and Status Functions

* **get_last_error_id()** - 
* **get_last_error()** - 
* **get_last_query()** - 
* **get_query_count()** - 
* **get_execution_time()** - 
* **get_affected_rows()** - 
* **get_last_id()** - 
* **get_log()** - 
* **get_last_log()** - 


## CRUD (MySQL Basic) Operations Functions

The basic mysql operations functions allow to execute the most common operations in databases.

* **insert(table, data)** - Insert a register into a table. Returning: FALSE on ERROR, TRUE/Last ID(Auto-increment) on SUCCESS.
* **update(table, data, where = null, parameters = array())** - Update a register in a table. Returning: FALSE on ERROR, Affected rows (0 is possible) on SUCCESS.
* **delete(table, where = null, parameters = array())** - Delete a register in a table. Returning: FALSE on ERROR, Affected rows (0 is possible) on SUCCESS.
* **select(table, data='*', where = null, operators='AND', parameters=array())** - Select registers in a table. Returning: FALSE on ERROR, Results fetched array on SUCCESS.

###Examples

####insert()
```php
<?php
$q = $db->insert("test",array("field"=>"field 001","value"=>"value 001","status"=>"1"));
print_r($q);
//Output: 1

$l = $db->get_last_query();
print_r($l);
//INSERT INTO `test` (`field`,`value`,`status`) VALUES ('field 001','value 001','1')
````

####update()
```php
<?php
$q = $db->update("test",array("field"=>"field 002"),array("field"=>"field 001"));
print_r($q);
//Output: 1 (Affected Rows) 

$l = $db->get_last_query();
print_r($l);
//UPDATE `test` SET `field` = 'field 002' WHERE `field` = 'field 001'
````

####delete()
```php
<?php
$q = $db->delete("test",array("field"=>"field 002"));
print_r($q);
//Output: 1 (Affected Rows) 

$l = $db->get_last_query();
print_r($l);
//DELETE FROM `test` WHERE `field` = 'field 002'
````

####select()
```php
<?php
//---------------- Method 1
$q = $db->select("test",array('field','value'));
print_r($q);
//Output: Array ( [0] => Array ( [field] => field 001 [value] => value 001 ) )

$l = $db->get_last_query();
print_r($l);
//SELECT `field`,`value` FROM `test`

//---------------- Method 2
$q = $db->select("test",'*',array('field'=>'like %f%','value'=>'is not null','status'=>'&1&'));
print_r($q);
//Output: Array ( [0] => Array ( [field] => field 001 [value] => value 001 [status] => 1 ) )

$l = $db->get_last_query();
print_r($l);
//SELECT * FROM `test` WHERE `field` like '%f%' AND `value` is not NULL AND `status` = 1

//---------------- Method 3
$q = $db->select("test",'*',array('status'=>'> &0&'));
print_r($q);
//Output: Array ( [0] => Array ( [field] => field 001 [value] => value 001 [status] => 1 ) )

$l = $db->get_last_query();
print_r($l);
//SELECT * FROM `test` WHERE `status` > 0
````
