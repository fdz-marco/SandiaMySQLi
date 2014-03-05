### Welcome to the Sandia MySQLi Database Wrapper 

# What is this?
*Sandia MySQLi* is a wrapper for MySQL Databases written in PHP to make simple some of the more recurrent tasks in databases management.

*Sandia MySQLi Database Wrapper* is maintained by Marco Fernandez member of the team inventtoo.com, please feel free to visite us and send your comments to us.

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

## Strings escape / \`Fields\` and 'Values' formatting

Sandia MySQL have some public methods to formatting queries. These ones are used inside the class but also can be used to formatting a query outside.

* *escape_string(string)* - alias from MYSQLI::real_escape_string
* *quote_field(string)* - quote a string with backquote, ex: \`string\`
* *quote_value(string)* - quote a string with single quote, ex: 'string'
* *quote_escaped_field(string)* - quote a string with backquote before escaped it
* *quote_escaped_value(string)* - quote a string before escaped it
* *quote_fields(string)* - quote an array of strings with backquote, ex: \`string\`,\`string\`,\`string\`
* *quote_values(string)* - quote an array of strings with single quote, ex: 'string','string','string'
* *quote_escaped_fields(string)* - quote an array of strings with backquote before escaped it
* *quote_escaped_values(string)* - quote an array of strings with single quote before escaped it
 
## Query parsing

The Sandia MySQL *String ecape and String formatting* are used to give format to the queries previous its use. Three are the most elemental functions.

* *quote_parameters(array('field'=>'value', 'field'=>'value')* - quote an array of \`field\` = 'value'
* *parse_query(string)* - parsing a query quoting ? ('') or ignoring quote #
* *parse_where(array('field'=>'value'), operators)* - quote where with the passed operators (AND as default). NULL is prepared to ignore quoting, you can also ignore quoting usin (&), ex: &string&.

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

## Queries 

