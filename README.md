### Welcome to the Sandia MySQLi Wrapper 

# What is this?
![Sandia MySQLi Wrapper](https://raw.github.com/fdz-marco/SandiaMySQLi/master/design/SandiaMySQLi_mini.png "Logo Sandia MySQLi") 

*Sandia MySQLi Wrapper* is a wrapper for MySQL Databases written in PHP to make simplier some of the more recurrent tasks in databases management.

This project have a MIT License, so you can modify it, redistribute it, print it, burn it, or whatever you want.

# Configuration

## How to setup Sandia MySQLi Wrapper?

Sandia MySQLi Wrapper is possible to be used as instance of an object:
```php
<?php
require_once 'Sandia_MySQLi_Wrapper.php';

$db = new SandiaMySQLi();
$db->open("localhost","user","password","database");
$q = $db->query("SELECT * FROM `table`");
echo $db->array_html_tabled($q);
$db->close();
```

Or directly calling the static functions of the class:
```php
<?php
require_once 'Sandia_MySQLi_Wrapper.php';

SandiaMySQLi::open("localhost","user","password","database");	
$q = SandiaMySQLi::query("SELECT * FROM `table`");
echo SandiaMySQLi::array_html_tabled($q);
SandiaMySQLi::close();
```

# Documentation

## Database Functions

| Function Name 													| Description 									|
| ----------------------------------------------------------------- | --------------------------------------------- | 
| **->open($host, $user, $pswd, $db, $port='', $charset='utf8')** 	| Open connection with the MySQL database. 		|
| **->close()** 													| Close connection with the MySQL database. 	|

##  MySQL Basic Functions :: CRUD/BREAD

The basic mysql operations functions allow to execute the most common operations in databases. The common operations normally are called as: **(C)** reate, **(R)** ead, **(U)** pdate, **(D)** elete; or **(B)** rowse, **(R)** ead, **(E)** dit, **(A)** dd, **(D)** elete.

| Function Name | Description |
| ----------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------- |
| **->select($table, $data='\*', $where = null, $operators='AND', $parameters = array())** 	| Select registers in a table. Returning: FALSE on ERROR, or results fetched array on SUCCESS. 			|
| **->insert($table, $data)** 																| Insert a register into a table. Returning: FALSE on ERROR, TRUE/Last ID(Auto-increment) on SUCCESS. 	|
| **->update($table, $data, $where = null, $operators='AND', $parameters = array())** 		| Update a register in a table. Returning: FALSE on ERROR, Affected rows (0 is possible) on SUCCESS. 	|
| **->delete($table, $where = null, $operators='AND', $parameters = array())** 				| Delete a register in a table. Returning: FALSE on ERROR, Affected rows (0 is possible) on SUCCESS. 	|

### Examples

#### select()
```php
<?php
//---------------- 
// Method 1
//---------------- 
$q = $db->select("test",array('field','value'));
print_r($q);
// Output: Array ( [0] => Array ( [field] => field 001 [value] => value 001 ) )

$l = $db->get_last_query();
print_r($l);
// Query generated: SELECT `field`,`value` FROM `test`

//---------------- 
// Method 2
//---------------- 
$q = $db->select("test",'*',array('field'=>'like %f%','value'=>'is not null','status'=>'&1&'));
print_r($q);
// Output: Array ( [0] => Array ( [field] => field 001 [value] => value 001 [status] => 1 ) )

$l = $db->get_last_query();
print_r($l);
// Query generated: SELECT * FROM `test` WHERE `field` like '%f%' AND `value` is not NULL AND `status` = 1

//---------------- 
// Method 3
//---------------- 
$q = $db->select("test",'*',array('status'=>'> &0&'));
print_r($q);
// Output: Array ( [0] => Array ( [field] => field 001 [value] => value 001 [status] => 1 ) )

$l = $db->get_last_query();
print_r($l);
// Query generated: SELECT * FROM `test` WHERE `status` > 0
```

[!TIP] 
To avoid scaping values in the WHERE part of the query use the character '&'.

#### insert()
```php
<?php
$q = $db->insert("test",array("field"=>"field 001","value"=>"value 001","status"=>"1"));
print_r($q);
// Output: 1

$l = $db->get_last_query();
print_r($l);
// Query generated: INSERT INTO `test` (`field`,`value`,`status`) VALUES ('field 001','value 001','1')
```

#### update()
```php
<?php
$q = $db->update("test",array("field"=>"field 002"),array("field"=>"field 001"));
print_r($q);
// Output: 1 (Affected Rows) 

$l = $db->get_last_query();
print_r($l);
// Query generated: UPDATE `test` SET `field` = 'field 002' WHERE `field` = 'field 001'
```

#### delete()
```php
<?php
$q = $db->delete("test",array("field"=>"field 002"));
print_r($q);
// Output: 1 (Affected Rows) 

$l = $db->get_last_query();
print_r($l);
// Query generated: DELETE FROM `test` WHERE `field` = 'field 002'
```

## Magic Static methods

[!TIP]
**Sandia MySQL Wrapper** makes also possible to execute CRUD/BREAD functions from static method directly using the name of the table.


| Function Format 										| Description 																							|
| ----------------------------------------------------- | ----------------------------------------------------------------------------------------------------- |
| **->\__select_from_\<tablename\>($arguments)** 		| Alias of **->select($table, $data='\*', $where = null, $operators='AND', $parameters = array())**. 	|
| **->>\__insert_into_\<tablename\>($arguments)** 		| Alias of **->insert($table, $data)**. 																|
| **->>\__update_\<tablename\>($arguments)** 			| Alias of **->update($table, $data, $where = null, $operators='AND', $parameters = array())**. 		|
| **->>\__delete_from_\<tablename\>($arguments)** 		| Alias of **->delete($table, $where = null, $operators='AND', $parameters = array())**. 				|

### Examples

In this example, queries are running over a table called **example**.

```php
<?php

SandiaMySQLi::open("localhost","root","","test");	
$w = SandiaMySQLi::__select_from_example();
echo SandiaMySQLi::array_html_tabled($w);

$w = SandiaMySQLi::__select_from_example('field1');
echo SandiaMySQLi::array_html_tabled($w);

$w = SandiaMySQLi::__insert_into_example(array('field1'=>'newvalue'));
echo SandiaMySQLi::array_html_tabled($w);

$w = SandiaMySQLi::__update_example(array('field1'=>'updatedvalue'), array('field1'=>'newvalue'));
echo SandiaMySQLi::array_html_tabled($w);

$w = SandiaMySQLi::__delete_from_example(array('field1'=>'updatedvalue'));
echo SandiaMySQLi::array_html_tabled($w);

```

## Getters / Setters

| Function Name 					| Description 														|
| --------------------------------- | ----------------------------------------------------------------- |
| **->get_cmd_connection()** 		| Return the connection in cmd format: _user@host:port>database_. 	|
| **->get_last_error_id()** 		| Return the last error id. 										|
| **->get_last_error()** 			| Return the last error message. 									|
| **->get_last_query()** 			| Return the last query in sql. 									|
| **->get_query_count()** 			| Return the number of queries executed in the connection. 			|
| **->get_time_execution()** 		| Return the execution time. 										|
| **->get_time_connection()** 		| Return the connection time. 										|
| **->get_time_last_query()** 		| Return the execution time of the last query. 						|
| **->get_affected_rows()** 		| Return the number of affected rows in the last query. 			|
| **->get_last_id()** 				| Return the last id affected in the last query. 					|
| **->get_log()** 					| Return the log history. 											|
| **->get_last_log()** 				| Return the last log entry. 										|
| **->set_log($value = true)** 		| Enable/Disable the logging. 										|

## Formatting Functions :: \`Fields\` and 'Values'

**Sandia MySQL Wrapper** have some methods to formatting queries. These ones are used inside the class but also can be used to formatting a query as static functions outside.

| Function Name 						| Description 																								  	|
| ------------------------------------- | ------------------------------------------------------------------------------------------------------------- |
| **->escape_string($string)** 			| Replace special characters, alias from **_MYSQLI::real_escape_string_** if connection exists. 				|
| **->quote_field($string)** 			| Quote a string with backquote. Example: **\`string\`** 														|
| **->quote_value($string)** 			| Quote a string with single quote. Example: **'string'** 														|
| **->quote_field_escaped($string)** 	| Quote a escaped string with backquote. Example: **\`escaped_string\`** 										|
| **->quote_value_escaped($string)** 	| Quote a escaped string with single quote. The **NULL** and text inside **&string&** will be excluded. Example: escaped_string => **'escaped_string'** or NULL=>**NULL** or  &random text&=>**random text** |
| **->quote_fields($string)** 			| Quote an **_array_** of strings with backquote. Example: **\`string\`,\`string\`,\`string\`** 				|
| **->quote_values($string)** 			| Quote an **_array_** of strings with single quote. Example: **'string','string','string'** 					|
| **->quote_fields_escaped($string)** 	| Quote an **_array_** of strings with backquote after escaped it. 												|
| **->quote_values_escaped($string)** 	| Quote an **_array_** of strings with single quote after escaped it. 											|

## Formatting Functions :: Query

**Sandia MySQL Wrapper** **_Formatting Functions :: Query_** are used to give format to fields and values previous of its use in a query. These functions are used to formatting the query itself.

| Function | Description |
| ----------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **->format_parameters(array('field'=>'value', 'field'=>'value')** | Format a quote an return and array of type: **_\`field\` = 'value'_**. The **value** could be use also the next operators: =,!=,>,<,>=,<=,is like,is not like. 	|
| **->format_simple_query(string)** 								| Parsing a query quoting the values: **?** for single quote ('') or **#** to ignoring quote. 																		|
| **->format_where_query (array('field'=>'value'), operators)** 	| Quote where with the passed operators (AND as default). **NULL** is prepared to ignore quoting. You can also ignore quoting using (&). Example: &string&. 		|

### Examples

#### format_parameters()
```php
<?php
$q = $db->format_parameters(array("name"=>"Marco","lastname"=>"Fernandez","Country"=>"Mexico"));
print_r($q);
// Use the internal methods quote_field_escaped & quote_value_escaped to create an array of parameters already quoted.
// Output:
//Array ( 
//[0] => `name` = 'Marco' 
//[1] => `lastname` = 'Fernandez' 
//[2] => `Country` = 'Mexico' 
//)
```

#### format_simple_query()
```php
<?php
$sql = 'SELECT email FROM users WHERE name=? AND user_id=# OR username=?';
$q = $db->format_simple_query($sql, array('marco',13));
print_r($q);
// Output: SELECT email FROM users WHERE name='marco' AND user_id=13
```

#### format_where_query()
```php
<?php
//---------------- 
// Sample 1
//---------------- 
$q = $db->format_where_query(array("dinasour"=>"t-rex","superhero"=>"batman"));
print_r($q);
// AND as default operator.
// Output: WHERE `dinasour` = 't-rex' AND `superhero` = 'batman' 

//---------------- 
// Sample 2
//---------------- 
$q = $db->format_where_query(array("dinasour"=>"t-rex","superhero"=>"batman","city"=>"NULL","id_vehicle"=>"&25&","lake"=>"like %bravo%"),array("AND","OR"));
print_r($q);
// AND as default operator if the operators array isn't of the same size.
// Output:  WHERE `dinasour` = 't-rex' AND `superhero` = 'batman' OR `city` = NULL AND `id_vehicle` = 25 AND `lake` like '%bravo%'

//---------------- 
// Sample 3
//---------------- 
$q = $db->format_where_query(array("dinasour"=>"t-rex","city"=>"NULL","id_vehicle"=>"> &25&"),"OR");
print_r($q);
// If the operator is a string is repeated in all the where query.
// Output: WHERE `dinasour` = 't-rex' OR `city` = NULL OR `id_vehicle` > 25
```

## Execution Functions

The **_Execution Functions_** realize three functions: query format and query execution.

| Function 											| Description 																	|
| ------------------------------------------------- | ----------------------------------------------------------------------------- |
| **->execute($sql, $parameters = array())** 		| Run a query in database, and parsing if a parameters array is given. 			|
| **->multi_execute($sql, $parameters = array())** 	| Run multiple queries in database, and parsing if a parameters array is given. |

[!IMPORTANT]  
These block of functions has also some private functions to internal use: **\_query($query)**, **\_multi_query($query)**, **\_log($transaction='')**

| Function 							| Description 																							|
| --------------------------------- | ----------------------------------------------------------------------------------------------------- |
| **->\_query($query)** 			| Send a unique query in the database connected. Alias from the public method **query($query)**. 		|
| **->\_multi_query($query)** 		| Send multiples queries in the database connected. Alias from the public method multi_query($query). 	|
| **->\_log($transaction='')** 		| Management of Log system. 																			|

## Fetch Functions
The **_Fetch Functions_** help to transfer the results of the executed query to an array.

#### Fetchs types
| Fectch Type Name    | Returned Data                                          |
| ------------------- |------------------------------------------------------- |
| MYSQLI_ALL          | `$data[#row][#column/column_name]`                     |
| MYSQLI_ROW_ASSOC    | `$data[#row][column_name]` ::Usually used::            |
| MYSQLI_ROW_NUM      | `$data[#row][#column]`                                 |
| MYSQLI_ROW_BOTH     | This is the equivalence of MYSQLI_ALL                  |
| MYSQLI_COLUMN_ASSOC | `$data[column_name][#row]` ::Field used::              |
| MYSQLI_COLUMN_NUM   | `$data[#column][#row]`                                 |
| MYSQLI_COLUMN_BOTH  | `$data[column_name/#column][#row/#row]` ::Not optimal::|

| Function Name | Description
| --- | --- |
| **->fetch($fetch=self::MYSQLI_ROW_ASSOC)** | Fetch the results of a single query. Alias from the private method **_fetch($fetch=self::MYSQLI_ROW_ASSOC)** | 
| **->fetch_multi($fetch=self::MYSQLI_ROW_ASSOC)** | Fetch the results of a multiple queries. Alias from the private method **_fetch_multi($fetch=self::MYSQLI_ROW_ASSOC)** |

[!IMPORTANT]  
These block of functions has also some private functions to internal use: **\_fetch($fetch=self::MYSQLI_ROW_ASSOC)**, **\_fetch_multi($fetch=self::MYSQLI_ROW_ASSOC)**, **\_fetch_row($i=0)**,  **\_fetch_column($i=0)**.

| Function 												| Description 										|
| ----------------------------------------------------- | ------------------------------------------------- |
| **->\_fetch($fetch=self::MYSQLI_ROW_ASSOC)** 			| Fetch the results of a single query. 				|
| **->\_fetch_multi($fetch=self::MYSQLI_ROW_ASSOC)** 	| Fetch the results of a multiple queries. 			|
| **->\_fetch_row($i=0)** 								| Use MYSQLI_ROW_ASSOC to return a row index. 		|
| **->\_fetch_column($i=0)** 							| Use MYSQLI_COLUMN_NUM to return a column index. 	|

### Examples

#### execute() / fetch()
```php
<?php
$q = $db->execute('SELECT * FROM test');
print_r($q);
// Output: 
// mysqli_result Object ( [current_field] => 0 [field_count] => 3 [lengths] => [num_rows] => 1 [type] => 0 ) 

$q = $db->fetch();
print_r($q);
// Output: 
// Array ( [0] => Array ( [field] => field 001 [value] => value 001 [status] => 1 ) )
```

## Auto-Fetching Queries Functions

The **_Auto-Fetching Queries Functions_** realize three functions: query format, query execution and query fetching. Therefore, they deliver directly and array with the results of the query.

| Function Name                           										| On Error   | On Success                          											|
| ----------------------------------------------------------------------------- | ---------- | ---------------------------------------------------------------------------- |
| **->query($sql, $parameters = array(), $fetch=self::MYSQLI_ROW_ASSOC)** 		| bool:false | MYSQLI_COLUMN_NUM => `$data[#row][column_name]` 		   						|
| **->multi_query($sql, $parameters = array(), $fetch=self::MYSQLI_ROW_ASSOC)** | bool:false | MYSQLI_COLUMN_NUM => `$data[#query][#row][column_name]` *if multiple queries	|
| **->query_single($sql, $parameters = array())**           					| bool:false | MYSQLI_COLUMN_NUM (FILTERED) = > `$data[0][0]` (0 is possible)   			|
| **->query_all($sql, $parameters = array())**               					| bool:false | MYSQLI_ALL (BOTH ROWS) => `$data[#row][#column/column_name]`         		|
| **->query_rows_assoc($sql, $parameters = array())**       					| bool:false | MYSQLI_ROW_ASSOC => `$data[#row][column_name]`          						|
| **->query_rows_num($sql, $parameters = array())**        						| bool:false | MYSQLI_ROW_NUM => `$data[#row][#column]`              						|
| **->query_rows_both($sql, $parameters = array())**        					| bool:false | MYSQLI_ROW_BOTH => `$data[#row][column_name/#column]`  						|
| **->query_columns_assoc($sql, $parameters = array())**     					| bool:false | MYSQLI_COLUMN_ASSOC => `$data[column_name][#row]`          					|
| **->query_columns_num($sql, $parameters = array())**      					| bool:false | MYSQLI_COLUMN_NUM => `$data[#column][#row]`              					|   
| **->query_columns_both($sql, $parameters = array())**     					| bool:false | MYSQLI_COLUMN_BOTH => `$data[column_name/#column][#row/#row]`       			|
| **->query_row($sql = null, $parameters = array(),$index=0)**        			| bool:false | MYSQLI_ROW_ASSOC (FILTERED) => `$data[index][column_name]`        			|
| **->query_column($sql = null, $parameters = array(),$index=0)**       		| bool:false | MYSQLI_COLUMN_NUM (FILTERED) =>`$data[index][#row]`                			|
| **->sp($sp, $parameters = array(), $fetch=self::MYSQLI_ROW_ASSOC)**           | bool:false | Execute a **CALL stored_procedure('param1','param2'..)** and fetch data.		|

##  Commit / Roll-back / Rewind / Free

| Function Name 					| Description 																		|
| --------------------------------- | --------------------------------------------------------------------------------- |
| **->transaction_begin()** 		| Disables **autocommit** to start a task without commit changes into the table. 	|
| **->transaction_commit()** 		| Executes a **commit** and enable the **autocommit** after that. 					|
| **->transaction_rollback()** 		| Executes a **rollback** and enable the **autocommit** after that. 				|
| **->rewind()**					| In data result it locates the cursor into position 0. 							|
| **->free()** 						| Free the results from a multi query. 												|

##  Array Operations 

| Function Name 				| Description 																|
| ----------------------------- | ------------------------------------------------------------------------- |
| **->array_swish($array)** 	| Switch an **array\[key1\]\[key2\]** to **array\[key2\]\[key1\]**. 		|
| **->array_tabled($array)** 	| Convert a results array into a html table to visualize results easier.	|

##  Query Functions

| Function Name 									| Description 									|
| ------------------------------------------------- | --------------------------------------------- |
| **->is_table($table)** 							| Verify if the table exist in the database. 	|
| **->is_field($table, $field)** 					| Verifiy if the field exists in the table. 	|
| **->get_tables()** 								| Get all the table names in the database. 		|
| **->get_fields($table, $get = self::FIELDS_ALL)** | Return all the fields of a table.				|
| **->get_next_autoincrement($table)** 				| Get the table next autoincrement number. 		|

#### Get types
| Get Type Name       | Returned Data                                          					|
| ------------------- |------------------------------------------------------------------------ |
| FIELDS_ALL          | **All** columns names (fields).											|
| FIELDS_AUTOFILLED   | Column names (fields) with **AUTO_INCREMENT** or default **NOT NULL**.	|
| FIELDS_REQUIRED     | Column names (fields) with **NOT AUTO_INCREMENT** and default **NULL**.	|
| FIELDS_PRIMARY      | Column name (field) of **PRIMARY** key. 								|
| FIELDS_UNIQUE       | Column names (fields) with **UNIQUE** constraint.						|
| FIELDS_FOREIGN      | Column names (fields) with **FOREIGN** key.								|

#### Examples
```php
<?php
require_once 'Sandia_MySQLi_Wrapper.php';

define("DBHOST", 'localhost');
define("DBPORT", 3306);
define("DBUSER", 'root');
define("DBPSWD", '');
define("DBNAME", 'test');

SandiaMySQLi::open(DBHOST,DBUSER,DBPSWD,DBNAME);	
var_dump( SandiaMySQLi::get_tables() );
var_dump( SandiaMySQLi::get_fields("table_name") );
var_dump( SandiaMySQLi::get_fields("table_name",SandiaMySQLi::FIELDS_REQUIRED) );
```

##  Table Queries Functions

| Function Name 															| Description 														|
| ------------------------------------------------------------------------- | ----------------------------------------------------------------- |
| **->get_properties($table, $get = self::FIELDS_ALL)** 					| Returns an array with the indexes: TABLE_NAME, COLUMN_NAME, COLUMN_ID, COLUMN_TYPE, DATA_TYPE, DATA_LENGHT, UNSIGNED, ZEROFILLED, ALLOW_NULL, COLUMN_DEFAULT, CONSTRAINT_TYPE, FOREIGN_DATABASE, FOREIGN_TABLE, FOREIGN_COLUMN, CONSTRAINTS; for each field in the table. |
| **->get_keys($table, $get = self::FIELDS_ALL, $constraints = false)** 	| Returns an array with the indexes: COLUMN_NAME, CONSTRAINT_NAME, CONSTRAINT_TYPE, COLUMN_ID, FOREIGN_DATABASE, FOREIGN_TABLE, FOREIGN_COLUMN; for each key in the table. |

##  Special Queries Functions

| Function Name 								| Description 																	|
| --------------------------------------------- | ----------------------------------------------------------------------------- |			
| **->table_backup($table,$backup='')** 		| Creates a backup of a table cloning it into other. 							|
| **->table_drop($table)** 						| Deletes (Drops) a table. 														|
| **->table_create($table_name, $fields)** 		| Creates a table using an array of fields. The format is in the next table. 	|

##  Fields keys for Table Creation

#### Basic Keys
| Key Name							| Description 								| Data Type														|
| --------------------------------- | ----------------------------------------- | ------------------------------------------------------------- |			
| **$fields[]['field_name']** 		| Field name. 								| (String) any string											|
| **$fields[]['type']** 			| Type of field. 							| (String) {CHAR, VARCHAR, TINYINT, INT, FLOAT, DOUBLE, ...}	|
| **$fields[]['length']** 			| If applies, length of field.				| (Integer)														|
| **$fields[]['unsigned']** 		| In case of number, if negative allowed.	| (Bool) TRUE/FALSE												|
| **$fields[]['zerofilled']** 		| In case of number, if right zerofilled.	| (Bool) TRUE/FALSE												|
| **$fields[]['allow_null']** 		| **NULL** value allowed.					| (Bool) TRUE/FALSE												|
| **$fields[]['default_value']** 	| Default value of field.					| (String) {AUTO_INCREMENT, CURRENT_TIMESTAMP, NULL} OR any string|
| **$fields[]['index']** 			| If field is a primary key or unique key.	| (String) {PK, UK} OR emtpy									|

#### Extra Keys as COMMENT 
| Key Name							| Description 								| Data Type														|
| --------------------------------- | ----------------------------------------- | ------------------------------------------------------------- |
| **$fields[]['identifier']** 		| Field Identifier. 						| (String) 														|
| **$fields[]['field_alias']** 		| Field Alias. 								| (String) 														|
| **$fields[]['field_description'** | Field Description. 						| (String)  													|
| **$fields[]['default_control']** 	| Default Value Control. 					| (String)   													|
| **$fields[]['format']** 			| If applies, format of value. 				| (String)   													|
| **$fields[]['length_min']** 		| Minimum Length. 							| (String)   													|
| **$fields[]['length_max']** 		| Maximum Length. 							| (String)   													|
| **$fields[]['range_min']** 		| Minimum of Range. 						| (String)   													|
| **$fields[]['range_max']** 		| Maximum of Range.							| (String)   													|
| **$fields[]['foreign_table']** 	| Foreign Table								| (String)   													|
| **$fields[]['foreign_index']** 	| Foreign Index								| (String)   													|
| **$fields[]['foreign_alias']** 	| Foreign Alias								| (String) 	  													|

#### Example
```php
<?php
define("DBHOST", 'localhost');
define("DBPORT", 3306);
define("DBUSER", 'root');
define("DBPSWD", '');
define("DBNAME", 'test');

SandiaMySQLi::open(DBHOST,DBUSER,DBPSWD,DBNAME);	
SandiaMySQLi::table_create('test_table',
	array(
		array('field_name'=>'id', 'type'=>'INT', 'length'=>10, 'unsigned'=>true, 'zerofilled'=>true, 'allow_null'=>false, 'default_value'=>'AUTO_INCREMENT', 'index' => 'PK'),
		array('field_name'=>'field1', 'type'=>'VARCHAR', 'length'=>10, 'unsigned'=>false, 'zerofilled'=>false, 'allow_null'=>true, 'default_value'=>'', 'index' => '',),
		array('field_name'=>'field2', 'type'=>'VARCHAR', 'length'=>20, 'unsigned'=>false, 'zerofilled'=>false, 'allow_null'=>true, 'default_value'=>'', 'index' => ''),
		array('field_name'=>'field3', 'type'=>'VARCHAR', 'length'=>30, 'unsigned'=>false, 'zerofilled'=>false, 'allow_null'=>true, 'default_value'=>'', 'index' => ''),
		array('field_name'=>'field4', 'type'=>'VARCHAR', 'length'=>40, 'unsigned'=>false, 'zerofilled'=>false, 'allow_null'=>true, 'default_value'=>'', 'index' => ''),
		array('field_name'=>'field5', 'type'=>'VARCHAR', 'length'=>50, 'unsigned'=>false, 'zerofilled'=>false, 'allow_null'=>true, 'default_value'=>'', 'index' => ''),
	)
);

```