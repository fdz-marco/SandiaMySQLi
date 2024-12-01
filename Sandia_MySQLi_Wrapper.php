<?php

## ############################################################### ##
##  ------------------- Sandia MySQLi Wrapper -------------------  ##
##                                                                 ##
##  @package     Sandia_MySQLi_Wrapper                             ##
##  @author      Marco Fernandez                                   ##
##  @link        marcofdz.com / glitcher.dev / inventtoo.com       ##
##  @link        http://github.com/fdz-marco                       ##
##  @version     4.0.0 (2024.12.01)                                ##
##  @license     http://opensource.org/licenses/MIT                ##
##  @copyright   2024 marcofdz.com / glitcher.dev / inventtoo.com  ##
##                                                                 ##
## ############################################################### ##

/*
****
	/======== Simple Example:::
		// Open Connection
		SandiaMySQLi::open(DBHOST,DBUSER,DBPSWD,DBNAME);
		
		// Execute auto-fetch query
		$q = SandiaMySQLi::query('SELECT * FROM test');
		echo SandiaMySQLi::array_html_tabled($q);
		
		// Magic-Static Method
		$q=SandiaMySQLi::select_test();
		echo SandiaMySQLi::array_html_tabled($q);
		
		// Print Log
		$log = SandiaMySQLi::get_log();
		echo SandiaMySQLi::array_html_tabled($log);

	// ======== Database
		open
		close
	// ======== Static
		__callStatic		
	// ======== Getters / Setters
		get_cmd_connection
		get_last_error_id
		get_last_error
		get_last_query
		get_query_count
		get_time_execution
		get_time_connection
		get_time_last_query
		get_affected_rows
		get_last_id
		get_log
		get_last_log
		set_log
	// ======== Formatting Functions :: `Fields` and 'Values'
		escape_string
		quote_field
		quote_value
		quote_field_escaped
		quote_value_escaped
		quote_fields
		quote_values
		quote_fields_escaped
		quote_values_escaped
	// ======== Formatting Functions :: Query
		format_parameters
		format_query_simple
		format_query_where
	// ========  Execution Functions
		_query
		_multi_query
		_log		
		execute
		multi_execute
	// ======== Fetch Functions
		_fetch
		_fetch_multi
		_fetch_row
		_fetch_column
		fetch					Public alias of function _fetch
		fetch					Public alias of function _fetch_multi
	// ======== Auto-Fetching Queries Functions
		query			      
		multi_query
		query_single>>        Error=false; Success: result string** (0 is possible)
		query_all>>           Error=false; Success:
		query_rows_assoc>>    Error=false; Success: $data[#row][column_name]
		query_rows_num>>      Error=false; Success: $data[#row][#column]
		query_rows_both>>     Error=false; Success: $data[#row][column_name/#column]
		query_columns_assoc>> Error=false; Success: $data[column_name][#row]
		query_columns_num>>   Error=false; Success: $data[#column][#row]
		query_columns_both>>  Error=false; Success:
		query_row>>           Error=false; Success: $data[i][column_name]
		query_column>>        Error=false; Success: $data[i][#row]
		sp
	// ========  MySQL Basic Functions :: CRUD/BREAD
		select>>			  Error=false; Success:results fetched array**
		insert>>		  	  Error=false; Success:True/Last ID(Auto-increment)
		delete>>			  Error=false; Success:affected rows (0 is possible)
		update>>			  Error=false; Success:affected rows (0 is possible)			
	// ======== Commit / Roll-back / Rewind / Free
		transaction_begin
		transaction_commit
		transaction_rollback
		rewind
		free
	// ======== Array Operations
		array_swish
		array_html_tabled
	// ======== Query Functions 
		is_table
		is_field
		get_tables
		get_fields
		get_next_autoincrement
	// ======== Table Queries Functions
		get_properties
		get_keys		
	// ======== Special Queries Functions 
		table_backup
		table_drop
		table_create			
		// Special Functions
		var_dump( SandiaMySQLi::get_tables() );
		var_dump( SandiaMySQLi::get_fields("table_name") );
****
*/

class SandiaMySQLi {
	
	const MYSQLI_ALL          = 3;	// MYSQLI_BOTH	= 3		$data[#row][#column/column_name]
	const MYSQLI_ROW_ASSOC    = 1;  // MYSQLI_ASSOC	= 1		$data[#row][column_name] ::Usually used::
	const MYSQLI_ROW_NUM      = 2;  // MYSQLI_NUM	= 2		$data[#row][#column]
	const MYSQLI_ROW_BOTH     = 3;  // MYSQLI_BOTH	= 3		This is the equivalence of MYSQLI_ALL
	const MYSQLI_COLUMN_ASSOC = 4;  // MYSQLI_ASSOC	= 1		$data[column_name][#row] ::Field used::
	const MYSQLI_COLUMN_NUM   = 5;  // MYSQLI_NUM	= 2		$data[#column][#row]
	const MYSQLI_COLUMN_BOTH  = 6;  // MYSQLI_BOTH	= 3		$data[column_name/#column][#row/#row] ::Not optimal::
	
	const FIELDS_ALL 	      = 0;  //
	const FIELDS_AUTOFILLED   = 1;  //
	const FIELDS_REQUIRED     = 2;  //
	const FIELDS_PRIMARY      = 3;  //
	const FIELDS_UNIQUE       = 4;  //
	const FIELDS_FOREIGN      = 5;  //
	
	const _CONNECTION_OPEN 		= 'CONNECTION_OPEN';
	
	private static $_host, $_port, $_user, $_pswd, $_db, $_connection;
	private static $_time_start, $_time_end, $_time_connection = 0, $_time_query = 0;
	private static $_logging = true, $_log = array(), $_query_counter = 0;
	private static $_sql, $_result = null;
	
	/*** 
	=========================================================
	Database 
	========================================================= 
	***/

	// Open connection with the MySQL database.
	public static function open($host, $user, $pswd, $db, $port='', $charset='utf8') {
		self::$_host	= ($host) ? $host : 'localhost';
		self::$_port	= ($port) ? $port : ini_get('mysqli.default_port');
		self::$_user	= ($user) ? $user : 'root';
		self::$_pswd	= ($pswd) ? $pswd : '';
		self::$_db		= ($db) ? $db : 'test';
		
		self::$_time_start 	= microtime(true);
		self::$_connection 	= new mysqli(self::$_host, self::$_user, self::$_pswd, self::$_db, self::$_port);
		self::$_time_end 	= microtime(true);
		self::_log(self::_CONNECTION_OPEN);
		
		if (self::$_connection->connect_error)
			throw new Exception('Connect Error (' . self::$_connection->connect_errno . ') ' . self::$_connection->connect_error);
		if ($charset)
			self::$_connection->set_charset($charset);
		return self::$_connection;
	}
	
	//  Close connection with the MySQL database.
	public static function close() {
		self::$_connection->close();
		self::$_result = null;
		self::$_connection = null;
		return true;
	}

	/*** 
	=========================================================
	Static - Magic Call 
	========================================================= 
	***/
	
	// CRUD/BREAD functions from static method directly using the name of the table.
	public static function __callStatic($name,$arguments){
		$regEx  = "/(__)?(select|insert|update|delete)_?(?:from|in|into)?_?([A-Za-z0-9_]+)/";
		preg_match_all($regEx, $name, $matches, PREG_PATTERN_ORDER);
		
		$log = isset($matches[1][0]) ? ((strtolower($matches[1][0])=='__') ? true : false) : false;
		$action = isset($matches[2][0]) ? strtolower($matches[2][0]) : false;
		$table = isset($matches[3][0]) ? strtolower($matches[3][0]) : false;
		if ($log) self::set_log(false);

		switch($action){
			case 'select':
				$data = !empty($arguments) ? (is_array($arguments[0]) ? $arguments[0] : $arguments) : '*';	
				$q = self::select($table,$data);
				self::set_log(); return $q; break;
			case 'insert':
				$data = !empty($arguments) ? (is_array($arguments[0]) ? $arguments[0] : $arguments) : '';
				$q = !empty($data) ? self::insert($table, $data) : false;
				self::set_log(); return $q; break;
			case 'delete':	
				$data = !empty($arguments) ? (is_array($arguments[0]) ? $arguments[0] : $arguments) : '';
				$q = !empty($data) ? self::delete($table, $data) : false;
				self::set_log(); return $q; break;
			case 'update':
				$data  = !empty($arguments) ? (is_array($arguments[0]) ? $arguments[0] : '') : '';
				$where = !empty($arguments) ? (is_array($arguments[1]) ? $arguments[1] : '') : '';
				$q = (!empty($data) && !empty($where)) ? self::update($table, $data, $where) : false;
				self::set_log(); return $q; break;
			default: 
				self::set_log(); return false; break;
		}
	}
		
	/***  
	========================================================= 
	Getters / Setters 
	========================================================= 
	***/

	// Return the connection in cmd format: _user@host:port>database_
	public static function get_cmd_connection(){
		//return 'shell>> mysql -h '.self::$_host.' -P '.self::$_port.' -u '.self::$_user.' -p '.self::$_pswd.' '.self::$_db;
		return self::$_user.'@'.self::$_host.':'.self::$_port.'>'.self::$_db;
	}
	
	// Return the last error id.
	public static function get_last_error_id() {
		return self::$_connection->errno;
	}

	// Return the last error message.
	public static function get_last_error() {
		return self::$_connection->error;
	}

	// Return the last query in sql.
	public static function get_last_query() {
		return self::$_sql;
	}

	// Return the number of queries executed in the connection.
	public static function get_query_count() {
		return self::$_query_counter;
	}

	// Return the execution time.
	public static function get_time_execution() {
		return number_format(self::$_time_end - self::$_time_start, 8);		 
	}
	
	// Return the connection time.
	public static function get_time_connection(){
		return self::$_time_connection;
	}
	
	// Return the execution time of the last query.
	public static function get_time_last_query(){
		return self::$_time_query;
	}
	
	// Return the number of affected rows in the last query.
	public static function get_affected_rows() {
		return self::$_connection->affected_rows;
	}

	// Return the last id affected in the last query.
	public static function get_last_id() {
		$id = self::$_connection->insert_id;
		// No ID generated in tables without auto-increment
		if ($id == 0) 
			return false; 
		// Only for tables with auto-increment
		return $id;
	}

	//  Return the log history.
	public static function get_log(){
		return self::$_log;
	}

	// Return the last log entry.
	public static function get_last_log(){
		return self::$_log[count(self::$_log)-1];
	}
	
	// Enable/Disable the logging.
	public static function set_log($value = true){
		return self::$_logging = $value;
	}
		
	/*** 
	========================================================= 
	Formatting Functions :: `Fields` and 'Values' 
	========================================================= 
	***/

	// Replace special characters, alias from **_MYSQLI::real_escape_string_** if connection exists.
	public static function escape_string($string) {
		return (self::$_connection) ? 
			self::$_connection->real_escape_string($string) :
				str_replace(array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a"), array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z"), $string);
	}
	
	// Quote a string with backquote. Example: `string`.
	public static function quote_field($string) {
		return '`' . $string . '`';
	}

	// Quote a string with single quote. Example: 'string'.
	public static function quote_value($string) {
		return "'" . $string . "'";
	}

	// Quote a escaped string with backquote. Example: `escaped_string` ==> escape_string(string).
	public static function quote_field_escaped($string) {
		return  "`" . self::escape_string($string) . "`";
	}

	// Quote a escaped string with single quote. The NULL and text inside &string& will be excluded.
	// Example: escaped_string => 'escaped_string' or NULL => NULL or &random text& => random text.
	public static function quote_value_escaped($string) {
		// NULL is a special case to ignore
		// Strings between &TEXT& are ignored to quoted
		$string = str_ireplace("NULL", '&NULL&', $string);
		return  preg_match("/&(.*)&/i",$string,$matches) ? self::escape_string($matches[1]) : "'" . self::escape_string($string) . "'";
	}

	// Quote an array of strings with backquote. Example: `string`,`string`,`string`.
	public static function quote_fields($fields) {
		return is_array($fields) ? array_map('self::quote_field', $fields) : 
			(is_string($fields) ? self::quote_field($fields) : false);
	}

	// Quote an array of strings with single quote. Example: 'string','string','string'.
	public static function quote_values($values) {
		return is_array($values) ? array_map('self::quote_value', $values) :
			(is_string ($values) ? self::quote_value($values) : false);
	}

	// Quote an array of strings with backquote after escaped it.
	public static function quote_fields_escaped($fields) {
		return is_array($fields) ? array_map('self::quote_field_escaped', $fields) : 
			(is_string($fields) ? self::quote_field_escaped($fields) : false);
	}

	// Quote an array of strings with single quote after escaped it.
	public static function quote_values_escaped($values) {
		return is_array($values) ? array_map('self::quote_value_escaped', $values) :
			(is_string ($values) ? self::quote_value_escaped($values) : false);
	}

	/*** 
	=========================================================
	Formatting Functions :: Query  
	========================================================= 
	***/

	// Format a quote an return and array of type: `field` = 'value'. 
	// The value could be use also the next operators: =,!=,>,<,>=,<=,is like,is not like.
	public static function format_parameters($data) {
		$out = array();
		// `field` = 'value' | `field` is (not) null | `field` (>|<|!=|=) 'value'
		if (!is_array($data))
			return false;
		foreach($data as $field => $value) {
			$operator = preg_match("/(\!\=?|\>(?:\=)?|\<(?:\=)?|is (?:not)?|(?:not)? ?like)/i", $value, $matches) ? $matches[0] : '=';
			$value = preg_replace("/(\!\=?|\>(?:\=)?|\<(?:\=)?|is (?:not)?|(?:not)? ?like)/i", '', $value);
			$out[] = self::quote_field_escaped($field) . " " . $operator . " " . self::quote_value_escaped($value);
		}
		return $out;
	}

	// Parsing a query quoting the values: ? for single quote ('') or # to ignoring quote.
	public static function format_query_simple($sql, $parameters) {
		if ((count($parameters) == 0)|!is_array($parameters))
			return $sql;
		$parts = explode('?', $sql);
		$query = '';
		while(count($parameters)) {
			$part = array_shift($parts);
			$quote_excluding = explode('#', $part);
			$query .= array_shift($quote_excluding);
			while (count($quote_excluding)) {
				$query .= array_shift($parameters).array_shift($quote_excluding);
			}
			if ($parameters) 
				$query .= self::quote_value_escaped(array_shift($parameters));
		}
		$query .= array_shift($parts);
		return $query;
	}

	// Quote where with the passed operators (AND as default). 
	// NULL is prepared to ignore quoting. You can also ignore quoting using (&). Example: &string&.
	public static function format_query_where($data, $operators='AND') {
		if (!is_array($data))
			return '';
		if (count($data) == 0)
			return '';
		$query = ' WHERE ';
		$params = self::format_parameters($data);
		$query .= array_shift($params);
		while(count($params)) {
			$query .= " # " . array_shift($params);
		}
		if(count($data) > 1){
			$operators = is_string($operators) ? array_fill(0, count($data)-1, $operators) : $operators;
			if (count($operators) < count($data)-1)
				$operators = array_merge($operators,array_fill(count($operators), count($data)-(count($operators)+1), 'AND'));
			$query = self::format_query_simple($query,$operators);
		}
		return $query;
	}

	/***  
	=========================================================
	Execution Functions 
	=========================================================
	***/
	
	// Run a query in database, and parsing if a parameters array is given.
	public static function execute($sql, $parameters = array()) {
		self::$_sql        = self::format_query_simple($sql, $parameters);
		self::$_time_start = microtime(true);
		self::$_result     = self::_query(self::$_sql);
		self::$_time_end   = microtime(true);
		self::$_query_counter++;
		self::_log();
		return self::$_result;
	}

	// Run multiple queries in database, and parsing if a parameters array is given.
	public static function multi_execute($sql, $parameters = array()) {
		self::$_sql        = self::format_query_simple($sql, $parameters);
		self::$_time_start = microtime(true);
		self::$_result     = self::_multi_query(self::$_sql);
		self::$_time_end   = microtime(true);
		self::_log();
		self::$_query_counter++;
		return self::$_result;
	}

	/***  
	=========================================================
	Private Functions: Execution
	=========================================================
	***/

	// Send a unique query in the database connected. Alias from the public method query($query).
	private static function _query($query) {
		
		return self::$_connection->query($query);
	}

	// Send multiples queries in the database connected. Alias from the public method multi_query($query). 
	private static function _multi_query($query) {
		return self::$_connection->multi_query($query);
	}

	// Management of Log system.
	private static function _log($transaction=''){
		$log = array();

		$log['id']				= count(self::$_log);
		$log['date']			= date("Y-m-d H:i:s");
		$log['query']			= empty($transaction) ? self::get_last_query() : self::get_cmd_connection();
		$log['execution_time'] 	= self::get_time_execution(). 's';
		$log['affected_rows'] 	= @self::get_affected_rows();
		$log['last_id'] 		= @self::get_last_id();
		$log['query_count'] 	= self::get_query_count() ? self::get_query_count() : '';
		$log['error_id']		= @self::get_last_error_id() ? @self::get_last_error_id() : '';
		$log['error']			= @self::get_last_error_id() ? @self::get_last_error() : '';
		
		self::$_time_query 		= $transaction!=self::_CONNECTION_OPEN ? self::get_time_execution() : self::$_time_connection;
		self::$_time_connection	= $transaction==self::_CONNECTION_OPEN ? self::get_time_execution() : self::$_time_connection;
		
		return (self::$_logging) ? array_push(self::$_log, $log) : false;
	}	

	/***  
	=========================================================
	Fetch Functions 
	=========================================================
	***/

	// Fetch the results of a single query.
	// Alias from the private method _fetch($fetch=self::MYSQLI_ROW_ASSOC).
	public static function fetch($fetch=self::MYSQLI_ROW_ASSOC) {
		return self::_fetch($fetch);
	}

	// Fetch the results of a multiple queries.
	// Alias from the private method _fetch_multi($fetch=self::MYSQLI_ROW_ASSOC).
	public static function fetch_multi($fetch=self::MYSQLI_ROW_ASSOC) {
		return self::_fetch_multi($fetch);
	}	
	
	/***  
	=========================================================
	Private Functions: Fetch
	=========================================================
	***/

	// Fetch the results of a single query.
	private static function _fetch($fetch=self::MYSQLI_ROW_ASSOC) {
		$data  = array();	
		if(!is_object(self::$_result)) return self::$_result;
		
		if ($fetch<=self::MYSQLI_ROW_BOTH) 			$_fetch = $fetch;					
		elseif ($fetch==self::MYSQLI_COLUMN_ASSOC) 	$_fetch = self::MYSQLI_ROW_ASSOC;	// (4) $data[column_name][#row] ::Field used:: 						==> (1) $data[#row][column_name] ::Usually used::
		elseif ($fetch==self::MYSQLI_COLUMN_NUM)	$_fetch = self::MYSQLI_ROW_NUM;		// (5) $data[#column][#row] 										==> (2) $data[#row][#column]
		elseif ($fetch==self::MYSQLI_COLUMN_BOTH)	$_fetch = self::MYSQLI_ROW_BOTH;	// (6) $data[column_name/#column][#row/#row] ::Not optimal:: 		==> (3) This is the equivalence of MYSQLI_ALL
	
		while ($tmp = self::$_result->fetch_array($_fetch)) $data[] = $tmp;
		if ($fetch>self::MYSQLI_ROW_BOTH) 
			$data = self::array_swish($data);
		self::$_result->free();
		self::free();
		return $data;
	}
	
	// Fetch the results of a multiple queries.
	private static function _fetch_multi($fetch=self::MYSQLI_ROW_ASSOC){
		$data  = array();
		$_data = array();
		if (!self::$_connection->more_results()) return false;
		
		if ($fetch<=self::MYSQLI_ROW_BOTH) 			$_fetch = $fetch;
		elseif ($fetch==self::MYSQLI_COLUMN_ASSOC) 	$_fetch = self::MYSQLI_ROW_ASSOC;
		elseif ($fetch==self::MYSQLI_COLUMN_NUM)	$_fetch = self::MYSQLI_ROW_NUM;
		elseif ($fetch==self::MYSQLI_COLUMN_BOTH)	$_fetch = self::MYSQLI_ROW_BOTH;
			
		while (self::$_connection->more_results()) {
			self::$_connection->next_result();
			if ($result = self::$_connection->store_result()) {
				while ($tmp = $result->fetch_array($_fetch)) $data[] = $tmp;
				if ($fetch>self::MYSQLI_ROW_BOTH) 
					$data = self::array_swish($data);
				$_data[] = $data;	
				$result->free();
				unset($data);
			}
		}
		self::free();
		return $_data;		
	}

	// Use MYSQLI_ROW_ASSOC to return a row index. 
	private static function _fetch_row($i=0) {
		$data = self::_fetch(self::MYSQLI_ROW_ASSOC);
		return isset($data[$i]) ? $data[$i] : false;
	}

	// Use MYSQLI_COLUMN_NUM to return a column index.
	private static function _fetch_column($i=0) {
		$data = self::_fetch(self::MYSQLI_COLUMN_NUM);
		return isset($data[$i]) ? $data[$i] : false;
	}	

	/***  
	=========================================================
	Auto-Fetching Queries Functions  
	=========================================================
	***/
	
	// Query Format, Query Execution and Query Fetching
	// MYSQLI_COLUMN_NUM =============> $data[#row][column_name]
	public static function query($sql, $parameters = array(), $fetch=self::MYSQLI_ROW_ASSOC){
		self::execute($sql, $parameters);
		if(!self::$_result) 
			return false;
		$data = self::_fetch($fetch);
		return $data;
	}
	
	// Query Format, Query Execution and Query Fetching
	// MYSQLI_COLUMN_NUM =============> $data[#query][#row][column_name] *if multiple queries
	public static function multi_query($sql, $parameters = array(), $fetch=self::MYSQLI_ROW_ASSOC){
		self::multi_execute($sql, $parameters);
		if(!self::$_result) 
			return false;
		$data = self::_fetch_multi($fetch);
		return (count($data)>1) ? $data : $data[0];
	}
	
	// Query Format, Query Execution and Query Fetching
	// MYSQLI_COLUMN_NUM (FILTERED) ==> $data[0][0]` (0 is possible) 
	public static function query_single($sql, $parameters = array()){					// $data[0][0]
		self::execute($sql, $parameters);
		if(!self::$_result) 
			return false; // Error
		$data = self::_fetch_column();
		return (isset($data[0]) ? $data[0] : false);
	}
	
	// Query Format, Query Execution and Query Fetching
	// MYSQLI_ALL (BOTH ROWS) ========> $data[#row][#column/column_name] 
	public static function query_all($sql, $parameters = array()) {						// ALL: $data[#row][#column/column_name] 
		self::execute($sql, $parameters);
		if(!self::$_result) 
			return false; // Error
		return self::_fetch(self::MYSQLI_ALL);
	}

	// Query Format, Query Execution and Query Fetching
	// MYSQLI_ROW_ASSOC ==============> $data[#row][column_name]
	public static function query_rows_assoc($sql, $parameters = array()) {				// $data[#row][column_name]
		self::execute($sql, $parameters);
		if(!self::$_result) 
			return false; // Error
		return self::_fetch(self::MYSQLI_ROW_ASSOC);
	}

	// Query Format, Query Execution and Query Fetching
	// MYSQLI_ROW_NUM ================> $data[#row][#column]
	public static function query_rows_num($sql, $parameters = array()) {     			// $data[#row][#column]
		self::execute($sql, $parameters);
		if(!self::$_result) 
			return false; // Error
		return self::_fetch(self::MYSQLI_ROW_NUM);
	}

	// Query Format, Query Execution and Query Fetching
	// MYSQLI_ROW_BOTH ===============> $data[#row][column_name/#column]
	public static function query_rows_both($sql, $parameters = array()) {    			// BOTH ROWS: $data[#row][column_name/#column]
		self::execute($sql, $parameters);
		if(!self::$_result) 
			return false; // Error
		return self::_fetch(self::MYSQLI_ROW_BOTH);
	}

	// Query Format, Query Execution and Query Fetching
	// MYSQLI_COLUMN_ASSOC ===========> $data[column_name][#row]
	public static function query_columns_assoc($sql, $parameters = array()) {			// $data[column_name][#row]
		self::execute($sql, $parameters);
		if(!self::$_result) 
			return false; // Error
		return self::_fetch(self::MYSQLI_COLUMN_ASSOC);
	}

	// Query Format, Query Execution and Query Fetching
	// MYSQLI_COLUMN_NUM =============> $data[#column][#row]
	public static function query_columns_num($sql, $parameters = array()) {  			// $data[#column][#row]
		self::execute($sql, $parameters);
		if(!self::$_result) 
			return false; // Error
		return self::_fetch(self::MYSQLI_COLUMN_NUM);
	}

	// Query Format, Query Execution and Query Fetching
	// MYSQLI_COLUMN_BOTH ============> $data[column_name/#column][#row/#row]
	public static function query_columns_both($sql, $parameters = array()) { 			// BOTH COLUMNS: $data[column_name/#column][#row/#row]
		self::execute($sql, $parameters);
		if(!self::$_result) 
			return false; // Error
		return self::_fetch(self::MYSQLI_COLUMN_BOTH);
	}

	// Query Format, Query Execution and Query Fetching
	// MYSQLI_ROW_ASSOC (FILTERED) ===> $data[index][column_name]
	public static function query_row($sql = null, $parameters = array(),$index=0) {		// $data[index][column_name]
		self::execute($sql, $parameters);
		if(!self::$_result) 
			return false; // Error
		return self::_fetch_row($index);
	}
	
	// Query Format, Query Execution and Query Fetching
	// MYSQLI_COLUMN_NUM (FILTERED) ==> $data[index][#row]
	public static function query_column($sql = null, $parameters = array(),$index=0) {	// $data[index][#row]
		self::execute($sql, $parameters);
		if(!self::$_result) 
			return false; // Error
		return self::_fetch_column($index);
	}	
	
	// Query Format, Query Execution and Query Fetching
	// Execute a **CALL stored_procedure('param1','param2'..)** and fetch data.	
	public static function sp($sp, $parameters = array(), $fetch=self::MYSQLI_ROW_ASSOC){
		if (is_string($sp) && (count($parameters)==0)) {
			if (strpos($sp, '(')!==false)
				return self::multi_query("CALL {$sp};", array(), $fetch);
			return self::multi_query("CALL {$sp}();", array(), $fetch);
		}
		elseif (is_string($sp) && (count($parameters)>0)) {
			$params = implode(',',self::quote_values_escaped($parameters));
			return self::multi_query("CALL {$sp}({$params});", array(), $fetch);
		}
		return false;
	}
	
	/***  
	=========================================================
	MySQL Basic Functions :: CRUD/BREAD
	=========================================================
	***/

	// Select registers in a table. Returning: FALSE on ERROR, or results fetched array on SUCCESS.
	public static function select($table, $data='*', $where = null, $operators='AND', $parameters = array()) {
		$sql  = 'SELECT ';
		$sql .= ((is_array($data)) ? implode(',', self::quote_fields($data)) : ((is_null($data)) ? '*' : $data));
		$sql .= ' FROM '. self::quote_field($table);
		$sql .= self::format_query_where($where, $operators);
		self::execute($sql, $parameters);
		if(!self::$_result) 
			return false; // Error
		$data = self::_fetch();
		return $data;
	}

	// Insert a register into a table. Returning: FALSE on ERROR, TRUE/Last ID(Auto-increment) on SUCCESS.
	public static function insert($table, $data) {
		$fields = (count(array_filter(array_keys($data), 'is_int'))==0) ? 
			self::quote_fields(array_keys($data)) : 
			self::quote_fields(array_values(self::get_fields($table, self::FIELDS_REQUIRED)));
		$values = self::quote_values_escaped(array_values($data));
		$sql = 'INSERT INTO ' . self::quote_field($table).' (' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';
		self::execute($sql);
		if(!self::$_result) 
			return false; // Error
		$id = self::get_last_id();
		if ($id === false) 
			return true; // No ID generated in tables without auto-increment
		return $id;
	}
	
	// Update a register in a table. Returning: FALSE on ERROR, Affected rows (0 is possible) on SUCCESS.
	public static function delete($table, $where = null, $operators='AND', $parameters = array()) {
		if (count(array_filter(array_keys($where), 'is_int'))>0){
			$keys = array_values(self::get_fields($table, self::FIELDS_REQUIRED));
			if (count($where) != count($keys))
				return false; // Error
			$where = array_combine($keys, $where);
		}
		$sql = 'DELETE FROM ' . self::quote_field($table);
		$sql .= self::format_query_where($where, $operators);
		self::execute($sql, $parameters);
		if(!self::$_result) 
			return false; // Error
		return self::get_affected_rows();
	}
	
	// Delete a register in a table. Returning: FALSE on ERROR, Affected rows (0 is possible) on SUCCESS.
	public static function update($table, $data, $where = null, $operators='AND', $parameters = array()) {
		if (count(array_filter(array_keys($data), 'is_int'))>0){
			$keys = array_values(self::get_fields($table, self::FIELDS_REQUIRED));
			if (count($data) != count($keys))
				return false; // Error
			$data = array_combine($keys, $data);
		}
		if (count(array_filter(array_keys($where), 'is_int'))>0){
			$keys = array_values(self::get_fields($table, self::FIELDS_REQUIRED));
			if (count($where) != count($keys))
				return false; // Error
			$where = array_combine($keys, $where);
		}
		$sql  = 'UPDATE ' . self::quote_field($table) . ' SET ';
		$sql .= implode(',', self::format_parameters($data));
		$sql .= self::format_query_where($where, $operators);
		self::execute($sql, $parameters);
		if(!self::$_result) 
			return false; // Error
		return self::get_affected_rows();
	}
		
	
	/*** 
	=========================================================
	Commit / Roll-back / Rewind / Free 
	=========================================================
	***/
	
	//  Disables autocommit to start a task without commit changes into the table.
	public static function transaction_begin() {
		return self::$_connection->autocommit(false);
	}

	// Executes a commit and enable the autocommit after that.
	public static function transaction_commit() {
		self::$_connection->commit();
		return self::$_connection->autocommit(true);
	}

	// Executes a rollback and enable the autocommit after that.
	public static function transaction_rollback() {
		self::$_connection->rollback();
		return self::$_connection->autocommit(true);
	}

	// In data result it locates the cursor into position 0. 
	public static function rewind() {
		if(self::$_result) 
			return self::$_result->data_seek(0);
		return false;
	}

	// Free the results from a multi query.
	public static function free() {
		while(self::$_connection->more_results()){
			self::$_connection->next_result();
			if($result = self::$_connection->store_result()){
				$result->free();
			}
		}
		return true;
	}

	/***  
	=========================================================
	Array Operations 
	=========================================================
	***/

	// Switch an array[key1][key2] to array[key2][key1].
	public static function array_swish($array) {
		$aux = array();
		foreach($array as $k1 => $a){
			foreach($a as $k2 => $v){
				$aux[$k2][$k1] = $v;
			}
		}
		return $aux;
	}
	
	// Convert a results array into a html table to visualize results easier.
	public static function array_html_tabled($array){
		$header  = "";
		$content = "";
		if (!is_array($array))
			return $array;
		foreach ($array as $key => $value){
			$value	  = (is_array($value)) ? self::array_html_tabled($value) : $value;
			$header  .= (is_string($key))  ? "<th><sup>{$key}<sup></th>" : "";
			$content .= (is_integer($key)) ? "<tr><th>{$key}</th><td>{$value}</td></tr>" : "<td>{$value}</td>";
		}	
		$header  = !empty($header) ? "<tr>{$header}</tr>" : "";
		$content = (strpos($content,"<tr>")===false) ? "<tr>{$content}</tr>" : $content;
		return "<table border='1' style='border-collapse:collapse;'>{$header}{$content}</table>";
	}	

	/*** 
	=========================================================
	Queries Functions 
	=========================================================
	***/
	
	// Verify if the table exist in the database.
	public static function is_table($table){
		$sql  = "SELECT count(`table_name`) as 'is_table' FROM information_schema.`tables` WHERE `table_schema`= DATABASE() and `table_name` = ?";
		return (self::query_single($sql,array($table)) > 0) ? true : false;
	}

	// Verifiy if the field exists in the table.
	public static function is_field($table, $field){
		$sql  = "SELECT count(`column_name`) as 'is_field' FROM information_schema.`columns` WHERE `table_schema`= DATABASE() and `table_name` = ? and `column_name` = ?";
		return (self::query_single($sql,array($table,$field)) > 0) ? true : false;
	}
	
	// Get all the table names in the database.
	public static function get_tables(){
		$sql  = "SHOW TABLES";
		return self::query_column($sql);
	}
	
	// Return all the fields of a table.
	public static function get_fields($table, $get = self::FIELDS_ALL){
		switch($get){
			case self::FIELDS_ALL:			$sql = 'SHOW COLUMNS FROM `#`'; break;
			case self::FIELDS_AUTOFILLED:	$sql = "SHOW COLUMNS FROM `#` WHERE `Extra` LIKE '%auto_increment%' OR `Default` IS NOT NULL"; break;
			case self::FIELDS_REQUIRED:		$sql = "SHOW COLUMNS FROM `#` WHERE `Extra` NOT LIKE '%auto_increment%' AND `Default` IS NULL"; break;
			case self::FIELDS_PRIMARY:		$sql = "SHOW COLUMNS FROM `#` WHERE `Key`='PRI'"; break;
			case self::FIELDS_UNIQUE:		$sql = "SELECT b.column_name FROM information_schema.TABLE_CONSTRAINTS a
													LEFT JOIN information_schema.KEY_COLUMN_USAGE b
													ON a.CONSTRAINT_NAME = b.CONSTRAINT_NAME AND a.table_schema = b.table_schema AND a.table_schema = b.table_schema
													WHERE a.table_schema = database() and a.table_name = ? AND a.constraint_type = 'UNIQUE' GROUP BY b.COLUMN_NAME";
											break;
			case self::FIELDS_FOREIGN:		$sql = "SELECT b.column_name FROM information_schema.TABLE_CONSTRAINTS a
													LEFT JOIN information_schema.KEY_COLUMN_USAGE b
													ON a.CONSTRAINT_NAME = b.CONSTRAINT_NAME AND a.table_schema = b.table_schema AND a.table_schema = b.table_schema
													WHERE a.table_schema = database() and a.table_name = ? AND a.constraint_type = 'FOREIGN KEY' GROUP BY b.COLUMN_NAME";
											break;
			default:						$sql = 'SHOW COLUMNS FROM `#`'; break;
		}
		return self::query_column($sql, array($table));
	}	
	
	// Get the table next autoincrement number.
	public static function get_next_autoincrement($table){
		$sql = "SELECT AUTO_INCREMENT FROM information_schema.`tables` WHERE `table_schema` = DATABASE() AND `table_name` = ?";
		$result = self::query_single($sql, array($table));
		return ($result === null) ? false : $result;
	}
	
	/*** 
	=========================================================
	Table Queries Functions
	=========================================================
	***/
	
	// Returns an array with the indexes: 
	// TABLE_NAME, COLUMN_NAME, COLUMN_ID, COLUMN_TYPE, 
	// DATA_TYPE, DATA_LENGHT, UNSIGNED, ZEROFILLED, ALLOW_NULL, 
	// COLUMN_DEFAULT, CONSTRAINT_TYPE, FOREIGN_DATABASE, FOREIGN_TABLE, 
	// FOREIGN_COLUMN, CONSTRAINTS; for each field in the table.
	public static function get_properties($table, $get = self::FIELDS_ALL){
		switch($get){
			case self::FIELDS_ALL:         $get = ''; break;
			case self::FIELDS_AUTOFILLED:  $get = "HAVING COLUMN_DEFAULT IS NOT NULL"; break;
			case self::FIELDS_REQUIRED:    $get = "HAVING COLUMN_DEFAULT IS NULL"; break;
			case self::FIELDS_PRIMARY:     $get = "HAVING CONSTRAINT_TYPE LIKE '%PRIMARY%'"; break;
			case self::FIELDS_UNIQUE:      $get = "HAVING CONSTRAINT_TYPE LIKE '%UNIQUE%'"; break;
			case self::FIELDS_FOREIGN:     $get = "HAVING CONSTRAINT_TYPE LIKE '%FOREIGN%'"; break;
			default:  $get = ''; break;
		}
		$sql  = "SELECT
					a.TABLE_NAME, a.COLUMN_NAME, a.ORDINAL_POSITION as `COLUMN_ID`, a.COLUMN_TYPE, a.DATA_TYPE,
					CAST(SUBSTRING(a.`column_type`,(LOCATE('(',a.`column_type`)+1),(LOCATE(')',a.`column_type`)-LOCATE('(',a.`column_type`)-1)) as UNSIGNED) as `DATA_LENGHT`,
					IF(LOCATE('unsigned',a.`column_type`)>0, 'true', 'false') as `UNSIGNED`,
					IF(LOCATE('zerofill',a.`column_type`)>0, 'true', 'false') as `ZEROFILLED`,
					IF(a.`is_nullable`='YES', 'true', 'false') as `ALLOW_NULL`,
					IF(LOCATE('auto_increment',a.EXTRA)>0, 'AUTO_INCREMENT', IF(a.COLUMN_DEFAULT IS NOT NULL, a.COLUMN_DEFAULT, NULL)) as `COLUMN_DEFAULT`,
					GROUP_CONCAT(c.CONSTRAINT_TYPE SEPARATOR '|') as `CONSTRAINT_TYPE`,
					b.REFERENCED_TABLE_SCHEMA as `FOREIGN_DATABASE`,
					b.REFERENCED_TABLE_NAME as `FOREIGN_TABLE`,
					b.REFERENCED_COLUMN_NAME as `FOREIGN_COLUMN`,
					CONCAT('[', GROUP_CONCAT((SELECT CONCAT_WS(',',CONCAT('{\"CONSTRAINT_NAME\":\"',UCASE(c.CONSTRAINT_NAME),'\"'), 
					CONCAT('\"CONSTRAINT_TYPE\":\"',UCASE(c.CONSTRAINT_TYPE),'\"}'))) SEPARATOR ',') ,']') as CONSTRAINTS
				FROM information_schema.`COLUMNS` `a`
					LEFT JOIN information_schema.KEY_COLUMN_USAGE `b`
					ON a.TABLE_SCHEMA = b.TABLE_SCHEMA and a.TABLE_NAME = b.TABLE_NAME and a.COLUMN_NAME = b.COLUMN_NAME
					LEFT JOIN information_schema.TABLE_CONSTRAINTS `c`
					ON a.TABLE_SCHEMA = c.TABLE_SCHEMA and a.TABLE_NAME = c.TABLE_NAME  and b.CONSTRAINT_NAME = c.CONSTRAINT_NAME
				WHERE (a.`table_schema` = DATABASE() AND a.`table_name` = ?)
					GROUP BY a.TABLE_NAME, a.COLUMN_NAME, `COLUMN_ID` #
				ORDER BY a.TABLE_NAME, COLUMN_ID";
		return self::query_rows_assoc($sql,array($table, $get));
	}

	// Returns an array with the indexes: 
	// COLUMN_NAME, CONSTRAINT_NAME, CONSTRAINT_TYPE, COLUMN_ID, 
	// FOREIGN_DATABASE, FOREIGN_TABLE, FOREIGN_COLUMN; for each key in the table.
	public static function get_keys($table, $get = self::FIELDS_ALL, $constraints = false){
		$sql_c = ($constraints) ? ", CONCAT('[', GROUP_CONCAT((SELECT CONCAT_WS(',',CONCAT('{\"CONSTRAINT_NAME\":\"',UCASE(b.CONSTRAINT_NAME),'\"'), 
									CONCAT('\"CONSTRAINT_TYPE\":\"',UCASE(a.CONSTRAINT_TYPE),'\"}'))) SEPARATOR ',') ,']') as CONSTRAINTS " : "";
		$sql_g = ($constraints) ? " GROUP BY b.COLUMN_NAME" : "";
		switch($get){
			case self::FIELDS_ALL:		$sql_s = ''; break;
			case self::FIELDS_PRIMARY:	$sql_s = " AND CONSTRAINT_TYPE = 'PRIMARY KEY' "; break;
			case self::FIELDS_UNIQUE:	$sql_s = " AND CONSTRAINT_TYPE = 'UNIQUE' "; break;
			case self::FIELDS_FOREIGN:	$sql_s = " AND CONSTRAINT_TYPE = 'FOREIGN KEY' "; break;
			default:					$sql_s = ''; break;
		}
		$sql  = "SELECT b.COLUMN_NAME, a.CONSTRAINT_NAME, a.CONSTRAINT_TYPE, b.ORDINAL_POSITION as `COLUMN_ID`, 
					b.REFERENCED_TABLE_SCHEMA as `FOREIGN_DATABASE`, b.REFERENCED_TABLE_NAME as `FOREIGN_TABLE`, b.REFERENCED_COLUMN_NAME as `FOREIGN_COLUMN` {$sql_c}		
					FROM information_schema.TABLE_CONSTRAINTS a
					LEFT JOIN information_schema.KEY_COLUMN_USAGE b
					ON (a.CONSTRAINT_SCHEMA = b.CONSTRAINT_SCHEMA AND a.TABLE_NAME = b.TABLE_NAME AND a.CONSTRAINT_NAME = b.CONSTRAINT_NAME)
					WHERE a.CONSTRAINT_SCHEMA = DATABASE() AND a.TABLE_NAME = ? {$sql_s} {$sql_g}";		
		return self::query_rows_assoc($sql,array($table));
	}

	/*** 
	=========================================================
	Special Queries Functions
	=========================================================
	***/

	// Creates a backup of a table cloning it into other.
	public static function table_backup($table,$backup=''){     
		$backup = empty($backup) ? $table."_".date("ymd") : $backup; 
		$sql = "CREATE TABLE `#` SELECT * FROM `#` "; 
		return (self::execute($sql, array($backup,$table))) ? true : false;
	} 
	
	// Deletes (Drops) a table. 	
	public static function table_drop($table){     
		$sql = "DROP TABLE `#`"; 
		return (self::execute($sql, array($table))) ? true : false;
	} 
	
	// Creates a table using an array of fields. 
	// The array of fields should follow next format:
	/*
	----> Basic Keys
	$fields[]['field_name'] 		= (String) any string
	$fields[]['type']				= (String) {CHAR, VARCHAR, TINYINT, INT, FLOAT, DOUBLE, ...}
	$fields[]['length'] 			= (Integer)
	$fields[]['unsigned']			= (Bool) TRUE/FALSE
	$fields[]['zerofilled'] 		= (Bool) TRUE/FALSE
	$fields[]['allow_null'] 		= (Bool) TRUE/FALSE
	$fields[]['default_value'] 		= (String) {AUTO_INCREMENT, CURRENT_TIMESTAMP, NULL} OR any string
	$fields[]['index'] 				= (String) {PK, UK}
	----> Extra Keys as COMMENT
	$fields[]['identifier'] 		= (String) 
	$fields[]['field_alias'] 		= (String) 
	$fields[]['field_description']	= (String) 
	$fields[]['default_control'] 	= (String) 
	$fields[]['format'] 			= (String) 
	$fields[]['length_min'] 		= (String) 
	$fields[]['length_max'] 		= (String) 
	$fields[]['range_min'] 			= (String) 
	$fields[]['range_max'] 			= (String) 
	$fields[]['foreign_table']		= (String) 
	$fields[]['foreign_index'] 		= (String) 
	$fields[]['foreign_alias'] 		= (String) 	
	*/
	public static function table_create($table_name, $fields){
		if (count($fields)==0) return false;
		$sql = "CREATE TABLE /*IF NOT EXISTS*/ `{$table_name}` \n( ";
		$idx_pk = "";
		$idx_uk = "";
		foreach($fields as $k=>$f){
			$field_name = isset($f['field_name']) ? $f['field_name'] : "field".$k;
			$type = isset($f['type']) ? $f['type'] : "VARCHAR";
			$length = isset($f['length']) ? $f['length'] : 64;
			$unsigned = (isset($f['unsigned']) && ($f['unsigned']===true)) ? "UNSIGNED" : "";
			$zerofilled = (isset($f['zerofilled']) && ($f['zerofilled']===true)) ? "ZEROFILL" : "";
			$allow_null = (isset($f['allow_null']) && ($f['allow_null']===true)) ? "NULL" : "NOT NULL";
			$default_value = 
				((isset($f['default_value']) && (strpos($f['default_value'], 'AUTO_INCREMENT') !== false)) ? "AUTO_INCREMENT" : 
				((isset($f['default_value']) && (strpos($f['default_value'], 'CURRENT_TIMESTAMP') !== false)) ? "DEFAULT CURRENT_TIMESTAMP" :
				((isset($f['default_value']) && (strpos($f['default_value'], 'NULL') !== false)) ? "DEFAULT NULL" :
				((isset($f['default_value']) && (!empty($f['default_value']))) ? "DEFAULT '{$f['default_value']}'" : ""))));
			$extra_keys = array();
				if (isset($f['identifier'])) 		$extra_keys["identifier"] 			=  $f['identifier'];
				if (isset($f['field_alias'])) 		$extra_keys["field_alias"] 			=  $f['field_alias'];
				if (isset($f['field_description'])) $extra_keys["field_description"] 	=  $f['field_description'];
				if (isset($f['default_control'])) 	$extra_keys["default_control"] 		=  $f['default_control'];
				if (isset($f['format'])) 			$extra_keys["format"] 				=  $f['format'];
				if (isset($f['length_min'])) 		$extra_keys["length_min"] 			=  $f['length_min'];
				if (isset($f['length_max'])) 		$extra_keys["length_max"] 			=  $f['length_max'];
				if (isset($f['range_min'])) 		$extra_keys["range_min"] 			=  $f['range_min'];
				if (isset($f['range_max'])) 		$extra_keys["range_max"] 			=  $f['range_max'];
				if (isset($f['foreign_table'])) 	$extra_keys["foreign_table"] 		=  $f['foreign_table'];
				if (isset($f['foreign_index'])) 	$extra_keys["foreign_index"] 		=  $f['foreign_index'];
				if (isset($f['foreign_alias'])) 	$extra_keys["foreign_alias"] 		=  $f['foreign_alias'];
			$comment = ((count($extra_keys)>0) ? "COMMENT '" . json_encode( $extra_keys ) . "'" : "");
			$sql .= "\t\t`{$field_name}` {$type}({$length}) {$unsigned} {$zerofilled} {$allow_null} {$default_value} {$comment}, \n";	
			$idx_pk .= (isset($f['index']) && (strpos($f['index'], "PK")!==false)) ? "`{$field_name}`, " : "";
			$idx_uk .= (isset($f['index']) && (strpos($f['index'], "UK")!==false)) ? "`{$field_name}`, " : "";
		}
		$idx_pk = substr($idx_pk,0,-2);
		$idx_uk = substr($idx_uk,0,-2);
		$sql.= (!empty($idx_pk)) ? "PRIMARY KEY ({$idx_pk}), " : "";
		$sql.= (!empty($idx_uk)) ? "UNIQUE INDEX `" . str_ireplace(array("`"," ","_",","), array("","","","_"), $idx_uk) . "` ({$idx_uk}), " : "";
		$sql = substr($sql,0,-2);
		$sql.= ") COLLATE='latin1_swedish_ci' ENGINE=InnoDB;";
		$result = self::execute($sql);
		echo $sql;
		return $result;
	}
	
}