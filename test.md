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

### Database
|Fn|Function Name|Input|
| --- | --- | --- |
|Database           |open                 |($host, $user, $pswd, $db, $port='', $charset='utf8')|
|Database           |close                |                                                     |
|Static             |callStatic           |($name,$arguments)|
|Getters / Setters  |get_cmd_connection   |-                                                    |
|Getters / Setters  |get_last_error_id    |-                                                    |
|Getters / Setters  |get_last_error       |-                                                    |
|Getters / Setters  |get_last_query       |-                                                    |
|Getters / Setters  |get_query_count      |-                                                    |
|Getters / Setters  |get_time_execution   |-                                                    |
|Getters / Setters  |get_time_connection  |-                                                    |
|Getters / Setters  |get_time_last_query  |-                                                    |
|Getters / Setters  |get_affected_rows    |-                                                    |
|Getters / Setters  |get_last_id          |-                                                    |
|Getters / Setters  |get_log              |-                                                    |
|Getters / Setters  |get_last_log         |-                                                   |
|Getters / Setters  |set_log              |($value = true)                                      |
|Transactions       |transaction_begin    |-                                                    |
|Transactions       |transaction_commit   |-                                                    |
|Transactions       |transaction_rollback |-                                                    |
|Transactions       |rewind               |-                                                    |
|Transactions       |free                 |-                                                    |

