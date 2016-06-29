# Welcome to the Cheesecake
### (MySQLi Database Wrapper)

## What is this?
*Cheesecake* is MySQL Databases wrapper written in PHP to make simple some of the more recurrent tasks in databases management.

*Cheesecake MySQLi Wrapper* is maintained by Marco Fernandez member of the team [inventtoo.com](http://inventtoo.com), please feel free to visit us and send your comments to us.

This project have a MIT License, so you can modify it, redistribute it, print it, burn it, or whatever you want.

## What make Cheesecake MySQL Wrapper better?
It's just lighter and smaller than the most libraries. And, who the hell don't love the cheesecakes?

# Configuration

## How to setup and connect *Cheesecake MySQLi Wrapper*

```php
<?php
require_once 'class_cheesecake.php';
$db = new cheesecake();
$db->open("localhost","user","password","database");
```

## Functions Catalog

### Getters / Setters
|Function Name|Input|Description|
| --- | --- | --- |
|get_cmd_connection   |()|*Return:: user@host:port>database |
|get_last_error_id    |()|Return:: $_connection->errno|
|get_last_error       |()|Return:: $_connection->err|
|get_last_query       |()|Return:: $_sql|
|get_query_count      |()|Return:: $_query_counter|
|get_time_execution   |()|Return:: Time Execution|
|get_time_connection  |()|*Return:: $_time_connection|
|get_time_last_query  |()|Return:: $_time_query|
|get_affected_rows    |()|Return:: $_connection->affected_rows|
|get_last_id          |()|*Return:: Last Auto-increment ID generated in tables|
|get_log              |()|Return:: $_log (Array)|
|get_last_log         |()|*Return:: $log[LAST]|
|set_log              |($value = true)|*Return:: boolean (Success: True)|

### Strings Escape Functions / 'Values' and \`Fields\` Formatting Functions
Cheesecake MySQLi Wrapper have some public methods to formatting queries. These ones are used inside the class but also can be used to formatting a query outside.

|Function Name|Input|Description|
| --- | --- | --- |
|escape_string        |($string)|Alias from MYSQLI::real_escape_string. Escape: "\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a".|
|quote_field          |($string)|Quote a string with backquote, ex: \`string\`|
|quote_value          |($string)|Quote a string with single quote, ex: 'string'|
|quote_escaped_field  |($string)|Quote a string with backquote before escaped it. Return:: \`escape_string()\`|
|quote_escaped_value  |($string)|Quote a string before escaped it. Return:: 'escape_string()'. Note: NULL is a special case to ignore. Strings between &TEXT& are ignored to quoted.|
|quote_fields         |($string)|Quote an array of strings with backquote, ex: \`string\`,\`string\`,\`string\`|
|quote_values         |($values)|Quote an array of strings with single quote, ex: 'string','string','string'|
|quote_escaped_fields |($string)|Quote an array of strings with backquote before escaped it.|
|quote_escaped_values |($values)|Quote an array of strings with single quote before escaped it.|

## Query Parsing Functions
The *Query Parsing Functions* are used to formatting the query itself.

|Function Name|Input|Description|
| --- | --- | --- |
|quote_parameters     |($data)|Input: array('field'=>'value', 'field'=>'value'). Quote an array of \`field\` = 'value'. Value can be:: is (not) null, (>|<|!=|=) 'value'|
|parse_query          |($sql, $parameters)|Parsing a query quoting ? ('') or ignoring quote #|
|parse_where          |($data, $operators='AND')|Input: (array('field'=>'value'), operators). Quote where with the passed operators (AND as default). NULL is prepared to ignore quoting, you can also ignore quoting using (&), ex: &string&.|

### Examples

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

## Execution Operations Functions
The execution queries realize two functions: parsing and execute the query.

|Function Name|Input|Description|
| --- | --- | --- |
|_query               |($query)|Return:: $_connection->query($query)|
|_multi_query         |($query)|Return:: $_connection->multi_query($query)|
|_log                 |($transaction='')|Add transaction to the log array.|
|execute              |($sql, $parameters = array())|Run a query in database, and parsing if a parameters array is given. **Using self::parse_query()|
|multi_execute        |($sql, $parameters = array())|Run multiple queries in database, and parsing if a parameters array is given. **Using self::parse_query()|

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
```
## Fetching Results   
|Function Name|Input|Description|
| --- | --- | --- |
|_fetch               |($fetch=self::MYSQLI_ROW_ASSOC)||
|_fetch_multi         |($fetch=self::MYSQLI_ROW_ASSOC)||
|_fetch_row           |($i=0)|Alias _fetch, return number of row.|
|_fetch_column        |($i=0)|Alias _fetch, return number of column.|
|fetch                |($fetch=self::MYSQLI_ROW_ASSOC)|Alias _fetch_multi.|

|Fetch Constant|Data Format|
| --- | --- |
|MYSQLI_ALL          |	$data[#row][#column/column_name]|
|MYSQLI_ROW_ASSOC    |  $data[#row][column_name] ::Usually used::|
|MYSQLI_ROW_NUM      |  $data[#row][#column]|
|MYSQLI_ROW_BOTH     |  This is the equivalence of MYSQLI_ALL|
|MYSQLI_COLUMN_ASSOC |  $data[column_name][#row] ::Field used::|
|MYSQLI_COLUMN_NUM   |  $data[#column][#row]|
| MYSQLI_COLUMN_BOTH |  $data[column_name/#column][#row/#row] ::Not optimal::|

## Query Functions 
The query functions execute and fetch results of the SQL queries.

|Function Name|Input|Description|
| --- | --- | --- |
|query                |($sql, $parameters = array(), $fetch=self::MYSQLI_ROW_ASSOC)||
|multi_query          |($sql, $parameters = array(), $fetch=self::MYSQLI_ROW_ASSOC)||
|query_single         |($sql, $parameters = array())|Error=false; Success: result string** (0 is possible)|
|query_all            |($sql, $parameters = array())|Error=false; Success:|
|query_rows_assoc     |($sql, $parameters = array())|Error=false; Success: $data[#row][column_name]|
|query_rows_num       |($sql, $parameters = array())|Error=false; Success: $data[#row][#column]|
|query_rows_both      |($sql, $parameters = array())|Error=false; Success: $data[#row][column_name/#column]|
|query_columns_assoc  |($sql, $parameters = array())|Error=false; Success: $data[column_name][#row]|
|query_columns_num    |($sql, $parameters = array())|Error=false; Success: $data[#column][#row]|
|query_columns_both   |($sql, $parameters = array())|Error=false; Success:|
|query_row            |($sql = null, $parameters = array(),$index=0)|Error=false; Success: $data[i][column_name]|
|query_column         |($sql = null, $parameters = array(),$index=0)|Error=false; Success: $data[i][#row]|
|sp                   |($sp, $parameters = array(), $fetch=self::MYSQLI_ROW_ASSOC)||

## CRUD (MySQLi Basic Operations) Functions
The basic MySQLi operations functions allow to execute the most common operations in databases.
(Create [Insert], Read [Select], Update, Delete)

|Function Name|Input|Description|
| --- | --- | --- |
|insert               |($table, $data)|Insert a register into a table. Returning: FALSE on ERROR, TRUE/Last ID(Auto-increment) on SUCCESS. Error=false; Success:True/Last ID(Auto-increment)|
|update               |($table, $data, $where = null, $operators='AND', $parameters = array())|Update a register in a table. Returning: FALSE on ERROR, Affected rows (0 is possible) on SUCCESS. Error=false; Success:affected rows (0 is possible)|
|delete               |($table, $where = null, $operators='AND', $parameters = array())|Delete a register in a table. Returning: FALSE on ERROR, Affected rows (0 is possible) on SUCCESS. Error=false; Success:affected rows (0 is possible)|
|select               |($table, $data='*', $where = null, $operators='AND', $parameters = array())|Select registers in a table. Returning: FALSE on ERROR, Results fetched array on SUCCESS. Error=false; Success:results fetched array**|

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
```

####update()
```php
<?php
$q = $db->update("test",array("field"=>"field 002"),array("field"=>"field 001"));
print_r($q);
//Output: 1 (Affected Rows)

$l = $db->get_last_query();
print_r($l);
//UPDATE `test` SET `field` = 'field 002' WHERE `field` = 'field 001'
```

####delete()
```php
<?php
$q = $db->delete("test",array("field"=>"field 002"));
print_r($q);
//Output: 1 (Affected Rows)

$l = $db->get_last_query();
print_r($l);
//DELETE FROM `test` WHERE `field` = 'field 002'
```

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
```

## Queries Functions
|Function Name|Input|Description|
| --- | --- | --- |
|is_table             |($table)||
|is_field             |($table,$field)||
|get_tables           |()||
|get_fields           |($table, $get = self::FIELDS_ALL)||
|get_next_autoincrement|($table)||

## Queries Functions
|Function Name|Input|Description|
| --- | --- | --- |
|get_properties       |($table, $get = self::FIELDS_AL)||
|get_keys             |($table, $get = self::FIELDS_ALL, $constraints = false)||

## Special Functions
|Function Name|Input|Description|
| --- | --- | --- |
|table_backup         |($table,$backup='')||
|table_drop           |($table)||
|table_create         |($table_name, $field)||

## Other Functions Catalog
|Group|Function Name|Input|Description|
| --- | --- | --- | --- |
|Database           |open                 |($host, $user, $pswd, $db, $port='', $charset='utf8')||
|Database           |close                |()||
|Static             |__callStatic         |($name,$arguments)||
| ----------------- | ----------------- | ----------------- | ----------------- |
|Transactions       |transaction_begin    |()||
|Transactions       |transaction_commit   |()||
|Transactions       |transaction_rollback |()||
|Transactions       |rewind               |()||
|Transactions       |free                 |()||
| ----------------- | ----------------- | ----------------- | ----------------- |
|Table              |array_swish          |($array)||
|Table              |array_tabled         |($array)||
