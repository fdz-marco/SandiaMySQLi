<?php

## ############################################################ ##
##  ------------------ Sandia MySQLi Wrapper -----------------  ##
##                                                              ##
##  @package     Sandia_MySQLi_Wrapper                          ##
##  @author      Marco Fernandez                                ##
##  @link        http://inventtoo.com                           ##
##  @link        http://github.com/inventtoo                    ##
##  @version     0.5.0 (2014.03.05)                             ##
##  @license     http://opensource.org/licenses/MIT             ##
##  @copyright   2014 inventtoo.com                             ##
##                                                              ##
## ############################################################ ##

/**
****
	/======== Database
		open
		close
	/======== Getters
		get_last_error_id
		get_last_error
		get_last_query
		get_query_count
		get_execution_time
		get_affected_rows
		get_last_id
		get_log
		get_last_log
	/======== Commit / Roll-back / Rewind / Free
		transaction_begin
		transaction_commit
		transaction_rollback
		rewind
		free
	/======== Strings escape / `Fields` and 'Values' formatting
		escape_string
		quote_field
		quote_value
		quote_escaped_field
		quote_escaped_value
		quote_fields
		quote_values
		quote_escaped_fields
		quote_escaped_values
	/======== Query parsing
		quote_parameters
		parse_query
		parse_where
	/======== Execution Operations
		_query
		_multi_query
		_log
		execute
		multi_execute
	/======== MySQL Operations
		insert>>			Error=false; Success:True/Last ID(Auto-increment)
		update>>			Error=false; Success:affected rows (0 is possible)
		delete>>			Error=false; Success:affected rows (0 is possible)
		select>>			Error=false; Success:results fetched array**
	/======== Fetching Results
		_fetch
		_fetch_row
		_fetch_column
		fetch		      Public alias of function _fetch
		fetch_single>>        Error=false; Success: result string** (0 is possible)
		fetch_all>>           Error=false; Success:
		fetch_rows>>          Error=false; Success: $data[#row][column_name]
		fetch_rows_num>>      Error=false; Success: $data[#row][#column]
		fetch_rows_both>>     Error=false; Success: $data[#row][column_name/#column]
		fetch_columns>>       Error=false; Success: $data[column_name][#row]
		fetch_columns_num>>   Error=false; Success: $data[#column][#row]
		fetch_columns_both>>  Error=false; Success:
		fetch_row>>           Error=false; Success: $data[i][column_name]
		fetch_column>>        Error=false; Success: $data[i][#row]
	/======== Queries Functions 
		call_sp
		is_table
		is_field
		get_tables
		get_fields
		get_next_autoincrement
	/======== Table Queries Functions
		get_table_properties
		get_table_indexes
	/======== Special Queries Functions 
		table_backup
		table_drop
		table_create
****
**/

class SandiaMySQLi {

	const MYSQLI_ALL          = 0;	// $data[#row][#column/column_name]
	const MYSQLI_ROW_ASSOC    = 1;  // $data[#row][column_name] ::Usually used::
	const MYSQLI_ROW_NUM      = 2;  // $data[#row][#column]
	const MYSQLI_ROW_BOTH     = 3;  // This is the equivalence of MYSQLI_ALL
	const MYSQLI_COLUMN_ASSOC = 4;  // $data[column_name][#row] ::Field used::
	const MYSQLI_COLUMN_NUM   = 5;  // $data[#column][#row]
	const MYSQLI_COLUMN_BOTH  = 6;  // $data[column_name/#column][#row/#row] ::Not optimal::
	const FIELDS_ALL          = 0;
	const FIELDS_AUTOFILLED   = 1;
	const FIELDS_REQUIRED     = 2;
	const FIELDS_PRIMARY      = 3;
	const FIELDS_UNIQUE       = 4;
	const FIELDS_FOREIGN      = 5;

	private $_connection;
	private $_sql;
	private $_query_count = 0;
	private $_result = null;
	private $_time_start;
	private $_time_end;
	private $_log;

	function __construct(){
		return ;
	}

	function __destruct(){
		return ;
	}

	/*** Database ***/

	public function open($host, $user, $password, $database, $charset='utf8') {
		$mysqli = new mysqli($host, $user, $password, $database);
		if ($mysqli->connect_error)
			throw new Exception('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		if ($charset)
			$mysqli->set_charset($charset);
		$this->_connection = $mysqli;
		return $this->_connection;
	}

	public function close() {
		$this->_connection->close();
	}

	/***  Getters ***/

	public function get_last_error_id() {
		return $this->_connection->errno ;
	}

	public function get_last_error() {
		return $this->_connection->error;
	}

	public function get_last_query() {
		return $this->_sql;
	}

	public function get_query_count() {
		return $this->_query_count;
	}

	public function get_execution_time() {
		return number_format($this->_time_end - $this->_time_start, 8) . " seconds";
	}

	public function get_affected_rows() {
		return $this->_connection->affected_rows;
	}

	public function get_last_id() {
		$id = $this->_connection->insert_id;
		if ($id == 0) return false; // No ID generated in tables without auto-increment
		return $id;
	}

	public function get_log(){
		return $this->_log;
	}

	public function get_last_log(){
		return $this->_log[$this->_query_count-1];
	}

	/*** Commit / Roll-back / Rewind / Free ***/

	public function transaction_begin() {
		$this->_connection->autocommit(false);
	}

	public function transaction_commit() {
		$this->_connection->commit();
		$this->_connection->autocommit(true);
	}

	public function transaction_rollback() {
		$this->_connection->rollback();
		$this->_connection->autocommit(true);
	}

	public function rewind() {
		if($this->_result) $this->_result->data_seek(0);
	}

	public function free(){
		while($this->_connection->more_results()){
			$this->_connection->next_result();
			if($result = $this->_connection->store_result()){
				$result->free();
			}
		}
	}

	/*** Strings escape / `Fields` and 'Values' formatting ***/

	public function escape_string($string) {
		return $this->_connection->real_escape_string($string);
	}

	public function quote_field($string) {
		return '`' . $string . '`';
	}

	public function quote_value($string) {
		return "'" . $string . "'";
	}

	public function quote_escaped_field($string) {
		return  "`" . $this->escape_string($string) . "`";
	}

	public function quote_escaped_value($string) {
		// NULL is a special case to ignore
		$string = str_ireplace("NULL", '&NULL&', $string);
		// Strings between &TEXT& are ignored to quoted
		return  preg_match("/&(.*)&/i",$string,$matches) ? $this->escape_string($matches[1]) : "'" . $this->escape_string($string) . "'";
	}

	public function quote_fields($fields) {
		return array_map(array($this, 'quote_field'), $fields);
	}

	public function quote_values($values) {
		return array_map(array($this, 'quote_value'), $values);
	}

	public function quote_escaped_fields($fields) {
		return array_map(array($this, 'quote_escaped_field'), $fields);
	}

	public function quote_escaped_values($values) {
		return array_map(array($this, 'quote_escaped_value'), $values);
	}

	/*** Query parsing ***/

	public function quote_parameters($data) {
		$out = array();
		foreach($data as $field => $value) {
			$operator = preg_match("/(\!\=?|\>(?:\=)?|\<(?:\=)?|is (?:not)?|(?:not)? ?like)/i",$value,$matches) ? $matches[0] : '=';
			$value = preg_replace("/(\!\=?|\>(?:\=)?|\<(?:\=)?| ?is ?(?:not)? ?| ?(?:not)? ?like ?)/i", '', $value);
			$out[] = $this->quote_field($field) . " " . $operator . " " . $this->quote_escaped_value($value);
		}
		return $out;
	}

	public function parse_query($sql, $parameters) {
		if(count($parameters) == 0) return $sql;
		$parts = explode('?', $sql);
		$query = '';
		while(count($parameters)) {
			$part = array_shift($parts);
			$quote_excluding = explode('#', $part);
			$query .= array_shift($quote_excluding);
			while (count($quote_excluding)) {
				$query .= array_shift($parameters).array_shift($quote_excluding);
			}
			if ($parameters) $query .= $this->quote_escaped_value(array_shift($parameters));
		}
		$query .= array_shift($parts);
		return $query;
	}

	public function parse_where($data, $operators='AND') {
		if(count($data) == 0) return '';
		$query = ' WHERE ';
		$params = $this->quote_parameters($data);
		$query .= array_shift($params);
		while(count($params)) {
			$query .= " # " . array_shift($params);
		}
		if(count($data) > 1){
			$operators = is_string($operators) ? array_fill(0, count($data)-1, $operators) : $operators;
			if (count($operators) < count($data)-1)
				$operators = array_merge($operators,array_fill(count($operators), count($data)-(count($operators)+1), 'AND'));
			$query = $this->parse_query($query,$operators);
		}
		return $query;
	}

	/***  Execution Operations ***/

	private function _query($query) {
		return $this->_connection->query($query);
	}

	private function _multi_query($query) {
		return $this->_connection->multi_query($query);
	}

	private function _log(){
		$id = $this->get_query_count();
		$this->_log[$id]['date'] = date("Y-m-d H:i:s");
		$this->_log[$id]['error_id'] = $this->get_last_error_id();
		$this->_log[$id]['error'] = ($this->get_last_error_id()) ? $this->get_last_error() : '';
		$this->_log[$id]['query'] = $this->get_last_query();
		$this->_log[$id]['affected_rows'] = $this->get_affected_rows();
		$this->_log[$id]['last_id'] = $this->get_last_id();
		$this->_log[$id]['execution_time'] = $this->get_execution_time();
		$this->_log[$id]['query_count'] = $this->get_query_count();
	}

	public function execute($sql, $parameters = array()) {
		$this->_sql        = $this->parse_query($sql, $parameters);
		$this->_time_start = microtime(true);
		$this->_result     = $this->_query($this->_sql);
		$this->_time_end   = microtime(true);
		$this->_log();
		$this->_query_count++;
		return $this->_result;
	}

	public function multi_execute($sql, $parameters = array()) {
		$this->_sql        = $this->parse_query($sql, $parameters);
		$this->_time_start = microtime(true);
		$this->_result     = $this->_multi_query($this->_sql);
		$this->_time_end   = microtime(true);
		$this->_log();
		$this->_query_count++;
		return $this->_result;
	}

	/***  MySQL Operations ***/

	public function insert($table, $data) {
		$fields = $this->quote_fields(array_keys($data));
		$values = $this->quote_escaped_values(array_values($data));
		$sql = 'INSERT INTO ' . $this->quote_field($table) . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';
		$this->execute($sql);
		if(!$this->_result) return false; // Error
		$id = $this->get_last_id();
		if ($id === false) return true; // No ID generated in tables without auto-increment
		return $id;
	}

	public function update($table, $data, $where = null, $parameters = array()) {
		$sql  = 'UPDATE ' . $this->quote_field($table) . ' SET ';
		$sql .= implode(',', $this->quote_parameters($data));
		$sql .= $this->parse_where($where, $operators='AND');
		$this->execute($sql, $parameters);
		if(!$this->_result) return false; // Error
		return $this->get_affected_rows();
	}

	public function delete($table, $where = null, $parameters = array()) {
		$sql = 'DELETE FROM ' . $this->quote_field($table);
		$sql .= $this->parse_where($where, $operators='AND');
		$this->execute($sql, $parameters);
		if(!$this->_result) return false; // Error
		return $this->get_affected_rows();
	}

	public function select($table, $data='*', $where = null, $operators='AND', $parameters = array()) {
		$sql  = 'SELECT ';
		$sql .= (is_array($data)) ? implode(',', $this->quote_fields($data)) : $data;
		$sql .= ' FROM '. $this->quote_field($table);
		$sql .= $this->parse_where($where, $operators);
		$this->execute($sql, $parameters);
		if(!$this->_result) return false; // Error
		$data = $this->_fetch();
		return $data;
	}

	/*** Fetching Results ***/

	private function _fetch($fetch=self::MYSQLI_ROW_ASSOC) {
		$data = array();
		$data_filtered = array();
		while ($tmp = $this->_result->fetch_array(MYSQLI_BOTH)) $data[] = $tmp;
		if ($fetch!=self::MYSQLI_ALL){
			foreach($data as $key => $array){
				foreach($array as $key2 => $value){
					switch($fetch){
						case self::MYSQLI_ROW_ASSOC:        // $data[#row][column_name]
							if (is_string($key2)) $data_filtered[$key][$key2] = $value;
							break;
						case self::MYSQLI_ROW_NUM:          // $data[#row][#column]
							if (is_string($key2)) $data_filtered[$key][] = $value;
							break;
						case self::MYSQLI_ROW_BOTH:			// This is the equivalence of MYSQLI_ALL
							if (is_string($key2)) $data_filtered[$key][$key2] = $value;   // $data[#row][column_name]
							if (is_string($key2)) $data_filtered[$key][] = $value;        // $data[#row][#column]
							break;
						case self::MYSQLI_COLUMN_ASSOC:     // $data[column_name][#row]
							if (is_string($key2)) $data_filtered[$key2][] = $value;
							break;
						case self::MYSQLI_COLUMN_NUM:       // $data[#column][#row]
							if (!is_string($key2)) $data_filtered[$key2][] = $value;
							break;
						case self::MYSQLI_COLUMN_BOTH:
							if (is_string($key2)) $data_filtered[$key2][] = $value;       // $data[column_name][#row]
							if (!is_string($key2)) $data_filtered[$key2][] = $value;      // $data[#column][#row]
							break;
						default: //default MYSQLI_ROW_ASSOC
							if (is_string($key2)) $data_filtered[$key][$key2] = $value;
							break;
					}
				}
			}
			$data = $data_filtered;
			unset($data_filtered);
		}
		$this->_result->free();
		$this->free();
		return $data;
	}

	private function _fetch_row($i=0) {
		$data = $this->_fetch(self::MYSQLI_ROW_ASSOC);
		return isset($data[$i]) ? $data[$i] : false;
	}

	private function _fetch_column($i=0) {
		$data = $this->_fetch(self::MYSQLI_COLUMN_NUM);
		return isset($data[$i]) ? $data[$i] : false;
	}

	public function fetch($fetch=self::MYSQLI_ROW_ASSOC) {
		return $this->_fetch($fetch);
	}
	
	public function fetch_single($sql, $parameters = array()){
		$this->execute($sql, $parameters);
		if(!$this->_result) return false; // Error
		$data = $this->_fetch_column();
		return (isset($data[0]) ? $data[0] : false);
	}

	public function fetch_all($sql, $parameters = array()) {          // ALL (BOTH ROWS)
		$this->execute($sql, $parameters);
		if(!$this->_result) return false; // Error
		return $this->_fetch(self::MYSQLI_ALL);
	}

	public function fetch_rows($sql, $parameters = array()) {         // $data[#row][column_name]
		$this->execute($sql, $parameters);
		if(!$this->_result) return false; // Error
		return $this->_fetch(self::MYSQLI_ROW_ASSOC);
	}

	public function fetch_rows_num($sql, $parameters = array()) {     // $data[#row][#column]
		$this->execute($sql, $parameters);
		if(!$this->_result) return false; // Error
		return $this->_fetch(self::MYSQLI_ROW_NUM);
	}

	public function fetch_rows_both($sql, $parameters = array()) {    // BOTH ROWS
		$this->execute($sql, $parameters);
		if(!$this->_result) return false; // Error
		return $this->_fetch(self::MYSQLI_ROW_BOTH);
	}

	public function fetch_columns($sql, $parameters = array()) {      // $data[column_name][#row]
		$this->execute($sql, $parameters);
		if(!$this->_result) return false; // Error
		return $this->_fetch(self::MYSQLI_COLUMN_ASSOC);
	}

	public function fetch_columns_num($sql, $parameters = array()) {  // $data[#column][#row]
		$this->execute($sql, $parameters);
		if(!$this->_result) return false; // Error
		return $this->_fetch(self::MYSQLI_COLUMN_NUM);
	}

	public function fetch_columns_both($sql, $parameters = array()) { // BOTH COLUMNS
		$this->execute($sql, $parameters);
		if(!$this->_result) return false; // Error
		return $this->_fetch(self::MYSQLI_COLUMN_BOTH);
	}

	public function fetch_row($sql = null, $parameters = array(),$index=0) {
		$this->execute($sql, $parameters);
		if(!$this->_result) return false; // Error
		return $this->_fetch_row($index);
	}

	public function fetch_column($sql = null, $parameters = array(),$index=0) {
		$this->execute($sql, $parameters);
		if(!$this->_result) return false; // Error
		return $this->_fetch_column($index);
	}

	/*** Queries Functions ***/

	public function call_sp($stored_procedure, $parameters = array(), $fetch=self::MYSQLI_ROW_ASSOC){
		$params = (count($parameters)>0) ? implode(',',$this->_quote_escape_strings($parameters)) : '';
		if ($fetch==self::MYSQLI_ROW_ASSOC)           // $data[#row][column_name]
			return $this->fetch_rows("CALL {$stored_procedure}({$params});");
		elseif ($fetch==self::MYSQLI_COLUMN_ASSOC)    // $data[column_name][#row]
			return $this->fetch_columns("CALL {$stored_procedure}({$params});");
		return false;
	}
	
	public function is_table($table){
		$sql  = "SELECT count(`table_name`) as 'is_table' FROM information_schema.`tables` WHERE `table_schema`= DATABASE() and `table_name` = ?";
		return ($this->fetch_single($sql,array($table)) > 0) ? true : false;
	}

	public function is_field($table,$field){
		$sql  = "SELECT count(`column_name`) as 'is_field' FROM information_schema.`columns` WHERE `table_schema`= DATABASE() and `table_name` = ? and `column_name` = ?";
		return ($this->fetch_single($sql,array($table,$field)) > 0) ? true : false;
	}
	
	public function get_tables(){
		$sql  = "SELECT `table_name` FROM information_schema.`tables` WHERE `table_schema` = DATABASE()";
		return $this->fetch_column($sql);
	}
	
	public function get_fields($table){
		$sql  = "SELECT `column_name` FROM information_schema.`columns` WHERE `table_schema` = DATABASE() and `table_name` = ?";
		return $this->fetch_column($sql,array($table));
	}
	
	public function get_next_autoincrement($table){
		$sql = "SELECT AUTO_INCREMENT FROM information_schema.`tables` WHERE `table_schema` = DATABASE() AND `table_name` = ?";
		$result = $this->fetch_single($sql, array($table));
		return ($result === null) ? false : $result;
	}
	
	/*** Table Queries Functions ***/

	public function get_table_properties($table, $getting = self::FIELDS_ALL){
		switch($getting){
			case self::FIELDS_ALL:         $getting = ''; break;
			case self::FIELDS_AUTOFILLED:  $getting = "HAVING default_value IS NOT NULL"; break;
			case self::FIELDS_REQUIRED:    $getting = "HAVING default_value IS NULL"; break;
			case self::FIELDS_PRIMARY:     $getting = "HAVING keys_type LIKE '%PRIMARY%'"; break;
			case self::FIELDS_UNIQUE:      $getting = "HAVING keys_type LIKE '%UNIQUE%'"; break;
			case self::FIELDS_FOREIGN:     $getting = "HAVING keys_type LIKE '%FOREIGN%'"; break;
			default:  $getting = ''; break;
		}
		$sql  = "SELECT
					i.`table_name`  as `table_name`,
					i.`column_name` as `field_name`,
					i.`ordinal_position` as `column_id`,
					i.`column_type` as `column_type`,
					i.`data_type` as `data_type`,
					SUBSTRING(i.`column_type`,(LOCATE('(',i.`column_type`)+1),(LOCATE(')',i.`column_type`)-LOCATE('(',i.`column_type`)-1)) as `length`,
					IF(LOCATE('unsigned',i.`column_type`)>0, 'true', 'false') as `unsigned`,
					IF(LOCATE('zerofill',i.`column_type`)>0, 'true', 'false') as `zerofilled`,
					IF(i.`is_nullable`='YES', 'true', 'false') as `allow_null`,
					IF(LOCATE('auto_increment',i.`extra`)>0, 'AUTO_INCREMENT', IF(i.`column_default` IS NOT NULL, i.`column_default`, NULL)) as `default_value`,
					GROUP_CONCAT(c.`constraint_type` SEPARATOR '|') as `keys_type`,
					k.`referenced_table_name` as `foreign_table`,
					k.`referenced_column_name` as `foreign_key`,
					CONCAT('[', GROUP_CONCAT((SELECT CONCAT_WS(',',CONCAT('{\"constraint_name\":\"',UCASE(c.`constraint_name`),'\"'),CONCAT('\"constraint_type\":\"',UCASE(c.`constraint_type`),'\"}'))) SEPARATOR ',') ,']') as constraints
				FROM information_schema.`COLUMNS` as `i`
					LEFT JOIN information_schema.KEY_COLUMN_USAGE as `k`
					ON i.TABLE_SCHEMA = k.TABLE_SCHEMA and i.TABLE_NAME = k.TABLE_NAME and i.COLUMN_NAME = k.COLUMN_NAME
					LEFT JOIN information_schema.TABLE_CONSTRAINTS as `c`
					ON i.TABLE_SCHEMA = c.TABLE_SCHEMA and i.TABLE_NAME = c.TABLE_NAME  and k.CONSTRAINT_NAME = c.CONSTRAINT_NAME
				WHERE (i.`table_schema` = database() AND i.`table_name` = ?)
					GROUP BY `table_name`, `field_name`, `column_id` # 
					ORDER BY `table_name`, `column_id`";
		return $this->fetch_rows($sql,array($table, $getting));
	}

	public function get_table_indexes($table){
		$sql  = "SELECT
					k.COLUMN_NAME as field_name,
					GROUP_CONCAT(c.`constraint_type` SEPARATOR '|') as `keys_type`,
					k.`referenced_table_name` as `foreign_table`,
					k.`referenced_column_name` as `foreign_key`,
					CONCAT('[', GROUP_CONCAT((SELECT CONCAT_WS(',',CONCAT('{\"constraint_name\":\"',UCASE(c.`constraint_name`),'\"'),CONCAT('\"constraint_type\":\"',UCASE(c.`constraint_type`),'\"}'))) SEPARATOR ',') ,']') as constraints
				FROM information_schema.TABLE_CONSTRAINTS as c
					LEFT JOIN information_schema.KEY_COLUMN_USAGE as k
					ON c.TABLE_SCHEMA = k.TABLE_SCHEMA and c.TABLE_NAME = k.TABLE_NAME AND c.CONSTRAINT_NAME = k.CONSTRAINT_NAME
				WHERE k.table_schema = database() and k.table_name = ? 
					GROUP BY k.COLUMN_NAME";
		return $this->fetch_rows($sql, array($table));
	}

	/*** Special Queries Functions ***/
	
	public function table_backup($table,$backup=''){     
		$backup = empty($backup) ? $table."_".date("ymd") : $backup; 
		$sql = "CREATE TABLE `#` SELECT * FROM `#` "; 
		return ($this->execute($sql, array($backup,$table))) ? true : false;
	} 
	
	public function table_drop($table){     
		$sql = "DROP TABLE `#`"; 
		return ($this->execute($sql, array($table))) ? true : false;
	} 
	
	public function table_create($table_name, $fields){
		if (count($fields)==0) return false;
		$sql = "CREATE TABLE /*IF NOT EXISTS*/ `{$table_name}` ( ";
		$idx_pk = "";
		$idx_uk = "";
		foreach($fields as $k=>$f){
			$field_name = isset($f['field_name']) ? $f['field_name'] : "field".$k;
			$type = isset($f['type']) ? $f['type'] : "VARCHAR";
			$length = isset($f['length']) ? $f['length'] : 64;
			$unsigned = (isset($f['length']) && ($f['length']===true)) ? "UNSIGNED" : "";
			$zerofilled = (isset($f['zerofilled']) && ($f['zerofilled']===true)) ? "ZEROFILL" : "";
			$allow_null = (isset($f['allow_null']) && ($f['allow_null']===true)) ? "NULL" : "NOT NULL";
			$default_value = 
				(isset($f['default_value']) && (strpos($f['default_value'], 'AUTO_INCREMENT'))) ? "AUTO_INCREMENT" : 
				(isset($f['default_value']) && (strpos($f['default_value'], 'CURRENT_TIMESTAMP'))) ? "DEFAULT CURRENT_TIMESTAMP" :
				(isset($f['default_value']) && (strpos($f['default_value'], 'NULL'))) ? "DEFAULT NULL" :
				(isset($f['default_value']) && (!empty($f['default_value']))) ? "DEFAULT '{$f['default_value']}'" : "";
			$comment = "COMMENT '" . 
				json_encode(
					array(
						"identifier"=> isset($f['identifier']) ? $f['identifier'] : "", 
						"field_alias"=> isset($f['field_alias']) ? $f['field_alias'] : "", 
						"field_description"=> isset($f['field_description']) ? $f['field_description'] : "", 
						"default_control"=> isset($f['default_control']) ? $f['default_control'] : "", 
						"format"=> isset($f['format']) ? $f['format'] : "", 
						"length_min"=> isset($f['length_min']) ? $f['length_min'] : "",
						"length_max"=> isset($f['length_max']) ? $f['length_max'] : "", 
						"range_min"=> isset($f['range_min']) ? $f['range_min'] : "",
						"range_max"=> isset($f['range_max']) ? $f['range_max'] : "",
						"foreign_table"=> isset($f['foreign_table']) ? $f['foreign_table'] : "",
						"foreign_index"=> isset($f['foreign_index']) ? $f['foreign_index'] : "",
						"foreign_alias"=> isset($f['foreign_alias']) ? $f['foreign_alias'] : ""
					)
				) . "'";
			$sql .= "\t\t`{$field_name}` {$type}({$length}) {$unsigned} {$zerofilled} {$allow_null} {$default_value} {$comment}, ";	
			$idx_pk .= (isset($f['index']) && (strpos($f['index'], "PK")!==false)) ? "`{$field_name}`, " : "";
			$idx_uk .= (isset($f['index']) && (strpos($f['index'], "UK")!==false)) ? "`{$field_name}`, " : "";
		}
		$idx_pk = substr($idx_pk,0,-2);
		$idx_uk = substr($idx_uk,0,-2);
		$sql.= (!empty($idx_pk)) ? "PRIMARY KEY ({$idx_pk}), " : "";
		$sql.= (!empty($idx_uk)) ? "UNIQUE INDEX `" . str_ireplace(array("`"," ","_",","), array("","","","_"), $idx_uk) . "` ({$idx_uk}), " : "";
		$sql = substr($sql,0,-2);
		$sql.= ") COLLATE='latin1_swedish_ci' ENGINE=InnoDB;";
		$result = $this->execute($sql);
		return $result;
	}
	
}
