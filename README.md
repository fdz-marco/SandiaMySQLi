### Welcome to the Turbine MySQLi DB Wrapper 

# What is this?
![Turbine MySQLi](https://raw.github.com/inventtoo/SandiaMySQLi/master/design/SandiaMySQLi_mini.png "Logo Sandia MySQLi") 

*Turbine MySQLi DB Wrapper* is a wrapper for MySQL Databases written in PHP to make simple some of the more recurrent tasks in databases management.

*Turbine MySQLi DB Wrapper* is maintained by Marco Fernandez member of the team [inventtoo.com](http://inventtoo.com), please feel free to visite us and send your comments to us.

This project have a MIT License, so you can modify it, redistribute it, print it, burn it, or whatever you want.

# Configuration

## How to setup Turbine MySQLi

Turbine MySQLi is possible to be used as instance of an object:
```php
<?php
require_once 'Turbine_MySQLi_Wrapper.php';

$db = new TurbineMySQL();
$db->open("localhost","user","password","database");
$q = $db->query("SELECT * FROM `table`");
echo $db->array_tabled($q);
$db->close();
```

Or directly calling the static functions of the class:
```php
<?php
require_once 'Turbine_MySQLi_Wrapper.php';

turbine::open("localhost","user","password","database");	
$q = turbine::query("SELECT * FROM `table`");
echo turbine::array_tabled($q);
turbine::close();
```

# Documentation

## Getters / Setters

| Function Name | Description |
| --- | --- |
| **->get_cmd_connection()** | return the connection in cmd format: _user@host:port>database_. |
| **->get_last_error_id()** | return the last error id. |
| **->get_last_error()** | return the last error message. |
| **->get_last_query()** | return the last query in sql. |
| **->get_query_count()** |return the number of queries executed in the connection. |
| **->get_time_execution()** | return the execution time. |
| **->get_time_connection()** | return the connection time. |
| **->get_time_last_query()** | return the execution time of the last query. |
| **->get_affected_rows()** | return the number of affected rows in the last query. |
| **->get_last_id()** | return the last id affected in the last query. |
| **->get_log()** | return the log history. |
| **->get_last_log()** | return the last log entry. |
| **->set_log(boolean)** | enable/disable the logging. |

## Formatting Functions :: \`Fields\` and 'Values'

**Turbine** have some **_public static_** methods to formatting queries. These ones are used inside the class but also can be used to formatting a query as static functions outside.

| Function Name | Description |
| --- | --- |
| **->escape_string(string)** | replace special characters, alias from **_MYSQLI::real_escape_string_** if connection exists. |
| **->quote_field(string)** | quote a string with backquote. Example: **\`string\`** |
| **->quote_value(string)** | quote a string with single quote. Example: **'string'** |
| **->quote_field_escaped(string)** | quote a escaped string with backquote. Example: **\`escaped_string\`** |
| **->quote_value_escaped(string)** | quote a escaped string with single quote. The **NULL** and text inside **&string&** will be excluded. Example: escaped_string => **'escaped_string'** or NULL=>**NULL** or  &random text&=>**random text** |
| **->quote_fields(string)** | quote an **_array_** of strings with backquote. Example: **\`string\`,\`string\`,\`string\`** |
| **->quote_values(string)** | quote an **_array_** of strings with single quote. Example: **'string','string','string'** |
| **->quote_fields_escaped(string)** | quote an **_array_** of strings with backquote after escaped it. |
| **->quote_values_escaped(string)** | quote an **_array_** of strings with single quote after escaped it. |

## Formatting Functions :: Query

**Turbine** **_Formatting Functions :: Query_** are used to give format to fields and values previous of its use in a query. These functions are used to formatting the query itself.

| Function | Description |
| --- | --- |
| **->format_parameters(array('field'=>'value', 'field'=>'value')** | format a quote an return and array of type: **_\`field\` = 'value'_**. The **value** field could be use also the next operators: =,!=,>,<,>=,<=,is like,is not like. |
| **->format_simple_query(string)** | parsing a query quoting the values: **?** for single quote ('') or **#** to ignoring quote. |
| **->format_where_query (array('field'=>'value'), operators)** | quote where with the passed operators (AND as default). **NULL** is prepared to ignore quoting. You can also ignore quoting using (&). Example: &string&. |

### Examples

#### format_parameters()
```php
<?php
$q = $db->format_parameters(array("name"=>"Marco","lastname"=>"Fernandez","age"=>23));
print_r($q);
//Use the internal methods quote_field_escaped & quote_value_escaped to create an array of parameters already quoted:
//Array ( 
//[0] => `name` = 'Marco' 
//[1] => `lastname` = 'Fernandez' 
//[2] => `age` = '23' 
//)
```
#### format_simple_query()
```php
<?php
$sql = 'SELECT email FROM users WHERE name=? AND user_id=# OR username=?';
$q = $db->format_simple_query($sql, array('marco',13));
print_r($q);
//SELECT email FROM users WHERE name='marco' AND user_id=13
```
#### format_where_query()
```php
<?php
$q = $db->format_where_query(array("dinasour"=>"t-rex","superhero"=>"batman"));
print_r($q);
//AND as default operator, OUTPUT:
//WHERE `dinasour` = 't-rex' AND `superhero` = 'batman' 

$q = $db->format_where_query(array("dinasour"=>"t-rex","superhero"=>"batman","city"=>"NULL","id_vehicle"=>"&25&","lake"=>"like %bravo%"),array("AND","OR"));
print_r($q);
//AND as default operator if the operators array isn't of the same size, OUTPUT:
//WHERE `dinasour` = 't-rex' AND `superhero` = 'batman' OR `city` = NULL AND `id_vehicle` = 25 AND `lake` like '%bravo%'

$q = $db->format_where_query(array("dinasour"=>"t-rex","city"=>"NULL","id_vehicle"=>"> &25&"),"OR");
print_r($q);
//If the operator is a string is repeated in all the where query.
//WHERE `dinasour` = 't-rex' OR `city` = NULL OR `id_vehicle` > 25
```
## Execution Functions

The **_Execution Functions_** realize two functions: query format and query execution. 

| Function | Description |
| --- | --- |
| **->execute (sql, parameters array)** | run a query in database, and parsing if a parameters array is given. |
| **->multi_execute(sql, parameters array)** | run multiple queries in database, and parsing if a parameters array is given. |

These block of functions has also some private functions to internal use: **\_query(sql)**, **\_multi_query(sql)**, **\_log(string)**

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
| **->fetch(fetch_type)** | | 
| **->fetch_multi(fetch_type)** |  |

These block of functions has also some private functions to internal use: **\_fetch(fetch_type)**, **\_fetch_multi(fetch_type)**, **\_fetch_row(int)**,  **\_fetch_column(int)**.

### Examples

#### execute() / fetch()
```php
<?php
$q = $db->execute('SELECT * FROM test');
print_r($q);
// mysqli_result Object ( [current_field] => 0 [field_count] => 3 [lengths] => [num_rows] => 1 [type] => 0 ) 

$q = $db->fetch();
print_r($q);
// Array ( [0] => Array ( [field] => field 001 [value] => value 001 [status] => 1 ) )
```

## Auto-Fetching Queries Functions

The **_Auto-Fetching Queries Functions_** realize three functions: query format, query execution and query fetching. Therefore, they deliver directly and array with the results of the query.

| Function Name                             | On Error   | On Success                          |
| ----------------------------------------- | ---------- | ----------------------------------- |
| **->query(sql,params,fetch_type)**        |            |                                     |
| **->multi_query(sql,params,fetch_type)**  |            |                                     |
| **->query_single(sql,params)**            | bool:false | `$data[0][0]` (0 is possible)       |
| **->query_all(sql,params)**               | bool:false | ALL (BOTH ROWS)                     |
| **->query_rows_assoc(sql,params)**        | bool:false | `$data[#row][column_name]`          |
| **->query_rows_num (sql,params)**         | bool:false | `$data[#row][#column]`              |
| **->query_rows_both (sql,params)**        | bool:false | `$data[#row][column_name/#column]`  |
| **->query_columns_assoc(sql,params)**     | bool:false | `$data[column_name][#row]`          |
| **->query_columns_num (sql,params)**      | bool:false | `$data[#column][#row]`              |   
| **->query_columns_both(sql,params)**      | bool:false | BOTH COLUMNS                        |
| **->query_row(sql,params,idx)**           | bool:false | `$data[i][column_name]`             |
| **->query_column(sql,params,idx)**        | bool:false | `$data[i][#row]`                    |
| **->sp(sp,params,fetch_type)**            | bool:false |                                     |

##  MySQL Basic Functions :: CRUD/BREAD

The basic mysql operations functions allow to execute the most common operations in databases. The common operations normally are called as: **(C)** reate, **(R)** ead, **(U)** pdate, **(D)** elete; or **(B)** rowse, **(R)** ead, **(E)** dit, **(A)** dd, **(D)** elete.

| Function Name | Description |
| --- | --- |
| **->select(table, data='*', where = null, operators='AND', parameters=array())** | Select registers in a table. Returning: FALSE on ERROR, or results fetched array on SUCCESS. |
| **->insert(table, data)** | Insert a register into a table. Returning: FALSE on ERROR, TRUE/Last ID(Auto-increment) on SUCCESS. |
| **->update(table, data,where = null, parameters = array())** | Update a register in a table. Returning: FALSE on ERROR, Affected rows (0 is possible) on SUCCESS. |
| **->delete(table, where = null, parameters = array())** | Delete a register in a table. Returning: FALSE on ERROR, Affected rows (0 is possible) on SUCCESS. |

### Examples

#### select()
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
```
#### insert()
```php
<?php
$q = $db->insert("test",array("field"=>"field 001","value"=>"value 001","status"=>"1"));
print_r($q);
//Output: 1

$l = $db->get_last_query();
print_r($l);
//INSERT INTO `test` (`field`,`value`,`status`) VALUES ('field 001','value 001','1')
```
#### update()
```php
<?php
$q = $db->update("test",array("field"=>"field 002"),array("field"=>"field 001"));
print_r($q);
//Output: 1 (Affected Rows) 

$l = $db->get_last_query();
print_r($l);
//UPDATE `test` SET `field` = 'field 002' WHERE `field` = 'field 001'
```
#### delete()
```php
<?php
$q = $db->delete("test",array("field"=>"field 002"));
print_r($q);
//Output: 1 (Affected Rows) 

$l = $db->get_last_query();
print_r($l);
//DELETE FROM `test` WHERE `field` = 'field 002'
```

##  Commit / Roll-back / Rewind / Free

| Function Name | Description |
| --- | --- |
| **->transaction_begin()** | |
| **->transaction_commit()** | |
| **->transaction_rollback()** | |
| **->rewind()** | |
| **->free()** | |

##  Array Operations 

| Function Name | Description |
| --- | --- |
| **->array_swish(array)** | |
| **->array_tabled(array)** | |

##  Query Functions

| Function Name | Description |
| --- | --- |
| **->is_table(table)** |Verify if the table exist in the database. |
| **->is_field(table,field)** |Verifiy if the field exists in the table. |
| **->get_tables** |Get all the table names in the database. |
| **->get_fields(table , gettype)** |Return all the fields of a table.|
| **->get_next_autoincrement(table)** | Get the table next autoincrement number.|

#### Get types
| Get Type Name       | Returned Data                                          |
| ------------------- |------------------------------------------------------- |
| FIELDS_ALL          | |
| FIELDS_AUTOFILLED   | |
| FIELDS_REQUIRED     | |
| FIELDS_PRIMARY      | |
| FIELDS_UNIQUE       | |
| FIELDS_FOREIGN      | |

#### Examples
```php
<?php
turbine::open(DBHOST,DBUSER,DBPSWD,DBNAME);	
var_dump( turbine::get_tables() );
var_dump( turbine::get_fields("table_name") );
var_dump( turbine::get_fields("table_name",turbine::FIELDS_REQUIRED) );
```
##  Table Queries Functions

| Function Name | Description |
| --- | --- |
|  **->get_properties(table, gettype)** | Returns an array with the indexes: TABLE_NAME,	COLUMN_NAME,	COLUMN_ID,	COLUMN_TYPE,	DATA_TYPE,	DATA_LENGHT,	UNSIGNED,	ZEROFILLED,	ALLOW_NULL,	COLUMN_DEFAULT,	CONSTRAINT_TYPE,	FOREIGN_DATABASE,	FOREIGN_TABLE,	FOREIGN_COLUMN,	CONSTRAINTS; for each field in the table. |
|  **->get_keys(table, gettype, constraints)** | Returns an array with the indexes: COLUMN_NAME,	CONSTRAINT_NAME,	CONSTRAINT_TYPE,	COLUMN_ID,	FOREIGN_DATABASE,	FOREIGN_TABLE,	FOREIGN_COLUMN; for each key in the table. |

##  Special Queries Functions

| Function Name | Description |
| --- | --- |
| **->table_backup(table, backup_name)** | |
| **->table_drop(table)** | |
| **->table_create(table, fields)** | |
