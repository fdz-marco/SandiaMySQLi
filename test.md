### Database
|Group|Function Name|Input|
| --- | --- | --- |
|Database           |open                 |($host, $user, $pswd, $db, $port='', $charset='utf8')|
|Database           |close                |()|
|Static             |callStatic           |($name,$arguments)|
|Getters / Setters  |get_cmd_connection   |()|
|Getters / Setters  |get_last_error_id    |()|
|Getters / Setters  |get_last_error       |()|
|Getters / Setters  |get_last_query       |()|
|Getters / Setters  |get_query_count      |()|
|Getters / Setters  |get_time_execution   |()|
|Getters / Setters  |get_time_connection  |()|
|Getters / Setters  |get_time_last_query  |()|
|Getters / Setters  |get_affected_rows    |()|
|Getters / Setters  |get_last_id          |()|
|Getters / Setters  |get_log              |()|
|Getters / Setters  |get_last_log         |()|
|Getters / Setters  |set_log              |($value = true)|
|Transactions       |transaction_begin    |()|
|Transactions       |transaction_commit   |()|
|Transactions       |transaction_rollback |()|
|Transactions       |rewind               |()|
|Transactions       |free                 |()|
|Strings escape     |escape_string        |($string)|
|Strings escape     |quote_field          |($string)|
|Strings escape     |quote_value          |($string)|
|Strings escape     |quote_escaped_field  |($string)|
|Strings escape     |quote_escaped_value  |($string)|
|Strings escape     |quote_fields         |($string)|
|Strings escape     |quote_values         |($values)|
|Strings escape     |quote_escaped_fields |($string)|
|Strings escape     |quote_escaped_values |($values)|
|Query parsing      |quote_parameters     |($data)|
|Query parsing      |parse_query          |($sql, $parameters)|
|Query parsing      |parse_where          |($data, $operators='AND')|
