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

### Getters / Setters
|Group|Function Name|Input|Description|
| --- | --- | --- | --- |
|Getters / Setters  |get_cmd_connection   |()|*Return:: user@host:port>database |
|Getters / Setters  |get_last_error_id    |()|Return:: $_connection->errno|
|Getters / Setters  |get_last_error       |()|Return:: $_connection->err|
|Getters / Setters  |get_last_query       |()|Return:: $_sql|
|Getters / Setters  |get_query_count      |()|Return:: $_query_counter|
|Getters / Setters  |get_time_execution   |()|Return:: Time Execution|
|Getters / Setters  |get_time_connection  |()|*Return:: $_time_connection|
|Getters / Setters  |get_time_last_query  |()|Return:: $_time_query|
|Getters / Setters  |get_affected_rows    |()|Return:: $_connection->affected_rows|
|Getters / Setters  |get_last_id          |()|*Return:: Last Auto-increment ID generated in tables|
|Getters / Setters  |get_log              |()|Return:: $_log (Array)|
|Getters / Setters  |get_last_log         |()|*Return:: $log[LAST]|
|Getters / Setters  |set_log              |($value = true)|*Return:: boolean (Success: True)|

### Strings escape Functions / \`Fields\` and 'Values' formatting Functions
Cheesecake MySQL have some public methods to formatting queries. These ones are used inside the class but also can be used to formatting a query outside.

|Group|Function Name|Input|Description|
| --- | --- | --- | --- |
| ----------------- | ----------------- | ----------------- | ----------------- |
|Strings Escape     |escape_string        |($string)|Alias from MYSQLI::real_escape_string|
|Strings Escape     |quote_field          |($string)|Quote a string with backquote, ex: \`string\`|
|Strings Escape     |quote_value          |($string)|Quote a string with single quote, ex: 'string'|
|Strings Escape     |quote_escaped_field  |($string)|Quote a string with backquote before escaped it|
|Strings Escape     |quote_escaped_value  |($string)|Quote a string before escaped it|
|Strings Escape     |quote_fields         |($string)|Quote an array of strings with backquote, ex: \`string\`,\`string\`,\`string\`|
|Strings Escape     |quote_values         |($values)|Quote an array of strings with single quote, ex: 'string','string','string'|
|Strings Escape     |quote_escaped_fields |($string)|Quote an array of strings with backquote before escaped it|
|Strings Escape     |quote_escaped_values |($values)|Quote an array of strings with single quote before escaped it|

## Strings escape Functions / \`Fields\` and 'Values' formatting Functions



* **escape_string(string)** - Alias from MYSQLI::real_escape_string
* **quote_field(string)** - Quote a string with backquote, ex: \`string\`
* **quote_value(string)** - Quote a string with single quote, ex: 'string'
* **quote_escaped_field(string)** - Quote a string with backquote before escaped it
* **quote_escaped_value(string)** - Quote a string before escaped it
* **quote_fields(string)** - Quote an array of strings with backquote, ex: \`string\`,\`string\`,\`string\`
* **quote_values(string)** - Quote an array of strings with single quote, ex: 'string','string','string'
* **quote_escaped_fields(string)** - Quote an array of strings with backquote before escaped it
* **quote_escaped_values(string)** - Quote an array of strings with single quote before escaped it
