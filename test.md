# Welcome to the Cheesecake MySQLi Database Wrapper

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

### Basic
|Group|Function Name|Input|Description|
| --- | --- | --- | --- |
|Database           |open                 |($host, $user, $pswd, $db, $port='', $charset='utf8')||
|Database           |close                |()||
|Static             |__callStatic         |($name,$arguments)||
### Getters / Setters
|Group|Function Name|Input|Description|
| --- | --- | --- | --- |
|Getters / Setters  |get_cmd_connection   |()||
|Getters / Setters  |get_last_error_id    |()||
|Getters / Setters  |get_last_error       |()||
|Getters / Setters  |get_last_query       |()||
|Getters / Setters  |get_query_count      |()||
|Getters / Setters  |get_time_execution   |()||
|Getters / Setters  |get_time_connection  |()||
|Getters / Setters  |get_time_last_query  |()||
|Getters / Setters  |get_affected_rows    |()||
|Getters / Setters  |get_last_id          |()||
|Getters / Setters  |get_log              |()||
|Getters / Setters  |get_last_log         |()||
|Getters / Setters  |set_log              |($value = true)||
### Transactions
|Group|Function Name|Input|Description|
| --- | --- | --- | --- |
|Transactions       |transaction_begin    |()||
|Transactions       |transaction_commit   |()||
|Transactions       |transaction_rollback |()||
|Transactions       |rewind               |()||
|Transactions       |free                 |()||
### Strings Escape  
|Group|Function Name|Input|Description|
| --- | --- | --- | --- |
|Strings Escape     |escape_string        |($string)||
|Strings Escape     |quote_field          |($string)||
|Strings Escape     |quote_value          |($string)||
|Strings Escape     |quote_escaped_field  |($string)||
|Strings Escape     |quote_escaped_value  |($string)||
|Strings Escape     |quote_fields         |($string)||
|Strings Escape     |quote_values         |($values)||
|Strings Escape     |quote_escaped_fields |($string)||
|Strings Escape     |quote_escaped_values |($values)||
### Query Parsing
|Group|Function Name|Input|Description|
| --- | --- | --- | --- |
|Query Parsing      |quote_parameters     |($data)||
|Query Parsing      |parse_query          |($sql, $parameters)||
|Query Parsing      |parse_where          |($data, $operators='AND')||
### Execution Operation
|Group|Function Name|Input|Description|
| --- | --- | --- | --- |
|Execution Operation|_query               |($query)||
|Execution Operation|_multi_query         |($query)||
|Execution Operation|execute              |($sql, $parameters = array())||
|Execution Operation|multi_execute        |($sql, $parameters = array())||
|Execution Operation|_log                 |($transaction='')||
### Fetching Results 
|Group|Function Name|Input|Description|
| --- | --- | --- | --- |
|Fetching Results   |_fetch               |($fetch=self::MYSQLI_ROW_ASSOC)||
|Fetching Results   |_fetch_multi         |($fetch=self::MYSQLI_ROW_ASSOC)||
|Fetching Results   |_fetch_row           |($i=0)||
|Fetching Results   |_fetch_column        |($i=0)||
|Fetching Results   |fetch                |($fetch=self::MYSQLI_ROW_ASSOC) *alias||
|Fetching Results   |fetch_multi          |($fetch=self::MYSQLI_ROW_ASSOC) *alias||
### Queries
|Group|Function Name|Input|Description|
| --- | --- | --- | --- |
|Queries            |query                |($sql, $parameters = array(), $fetch=self::MYSQLI_ROW_ASSOC)||
|Queries            |multi_query          |($sql, $parameters = array(), $fetch=self::MYSQLI_ROW_ASSOC)||
|Queries            |query_single         |($sql, $parameters = array())|Error=false; Success: result string** (0 is possible)|
|Queries            |query_all            |($sql, $parameters = array())| Error=false; Success:|
|Queries            |query_rows_assoc     |($sql, $parameters = array())|Error=false; Success: $data[#row][column_name]|
|Queries            |query_rows_num       |($sql, $parameters = array())|Error=false; Success: $data[#row][#column]|
|Queries            |query_rows_both      |($sql, $parameters = array())|Error=false; Success: $data[#row][column_name/#column]|
|Queries            |query_columns_assoc  |($sql, $parameters = array())|Error=false; Success: $data[column_name][#row]|
|Queries            |query_columns_num    |($sql, $parameters = array())|Error=false; Success: $data[#column][#row]|
|Queries            |query_columns_both   |($sql, $parameters = array())|Error=false; Success:|
|Queries            |query_row            |($sql = null, $parameters = array(),$index=0)|Error=false; Success: $data[i][column_name]|
|Queries            |query_column         |($sql = null, $parameters = array(),$index=0)|Error=false; Success: $data[i][#row]|
|Queries            |sp                   |($sp, $parameters = array(), $fetch=self::MYSQLI_ROW_ASSOC)||
### MySQL Operations
|Group|Function Name|Input|Description|
| --- | --- | --- | --- |
|MySQL Operations   |select                |($table, $data='*', $where = null, $operators='AND', $parameters = array())|Error=false; Success:results fetched array**|
|MySQL Operations   |insert                |($table, $data)|Error=false; Success:True/Last ID(Auto-increment)|
|MySQL Operations   |delete                |($table, $where = null, $operators='AND', $parameters = array())|Error=false; Success:affected rows (0 is possible)|
|MySQL Operations   |update                |($table, $data, $where = null, $operators='AND', $parameters = array())|Error=false; Success:affected rows (0 is possible)|

### MySQL Operations
|Group|Function Name|Input|Description|
| --- | --- | --- | --- |
