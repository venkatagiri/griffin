<?php

abstract class Model {
	private $_fields = array();
	private static $_db;
	
	function __construct() {
		self::$_db = Database::get_instance();
	}

	function __get($name) {
		if($name == "table_name") return self::table_name();
		if(isset($this->_fields[$name])) return $this->_fields[$name];
		return false;
	}

	function __set($name, $value) {
		$this->_fields[$name] = $value;
	}

	function __isset($name) {
		return isset($this->_fields[$name]);
	}

	private static function db() {
		if(!isset(self::$_db)) self::$_db = Database::get_instance();
		return self::$_db;
	}

	public static function table_name() {
		if(isset(static::$table_name)) return static::$table_name;
		else return Table::get_table_name(get_called_class());
	}

	public static function columns() {
		return Table::get_columns(self::table_name());
	}

	public static function find_with_pagination($where = '1 = 1', $current_page = 1, $per_page = 12) {
		$total_count = self::count($where);
		$pagination = new Pagination($current_page, $total_count, $per_page);
		
		$where_clause = $where;
		$where_clause .= " LIMIT " . $pagination->per_page;
		$where_clause .= " OFFSET " . $pagination->offset();
		return array($pagination, self::find($where_clause));
	}

	public static function delete_where($where = '1 != 1') {
		$sql = "DELETE FROM ".self::table_name();
		$sql .= " WHERE ".$where;
		self::db()->execute($sql);
		return true;
	}

	public static function find_by_id($id) {
		return self::find('first', 'id='.$id);
	}
	
	public static function find_all() {
		return self::find();
	}

	public static function find($limit = 'all', $where = '1 = 1') {
		$sql = "SELECT * FROM ".self::table_name();
		$sql .= " WHERE ".$where;
		if($limit == "first") $sql .= " LIMIT 1";
		$result = self::find_by_sql($sql);
		return $limit == "first" ? $result[0] : $result;
	}
	
	public static function find_by_sql($sql) {
		return self::db()->query($sql, get_called_class());
	}
	
	public static function count($where_clause="1 = 1") {
		$sql = "SELECT COUNT(1) FROM ".self::table_name();
		$sql .= " WHERE ".$where_clause;
		$result_array = self::db()->query($sql);
		return array_shift($result_array[0]);
	}

	protected function clean_attrs() {
		return $this->_fields;
		// $clean_attrs = array();
		// foreach($this->_fields as $key => $value) {
		// 	$clean_attrs[$key] = self::db()->prepare_value($value); #TODO: Review this.
		// }
		// return $clean_attrs;
	}

	// public function has_changed() {
	// 	if(!$this->id) return;
		
	// 	$db_attrs = self::find_by_id($this->id)->clean_attrs();
	// 	$attrs = $this->clean_attrs();
	// 	foreach($attrs as $key=>$value) {
	// 		if(preg_match("/^date/i", $key)) continue; // Don't consider dates while comparing.
	// 		if($db_attrs[$key] != $value) return true;
	// 	}
	// 	return false;
	// }
		
	public function save() {
		return isset($this->id)? $this->update() : $this->create();
	}
	
	public function create() {
		$attrs = $this->clean_attrs();
		$sql = "INSERT INTO ".self::table_name()."( `";
		$sql .= join("`, `", array_keys($attrs));
		$sql .= "`) VALUES ( '";
		$sql .= join("', '", array_values($attrs));
		$sql .= "')";
		if(self::db()->execute($sql)) {
			$this->id = self::db()->insert_id();
			return true;
		}
		return false;
	}

	public function update() {
		$attrs = $this->clean_attrs();
		$attr_pairs = array();
		foreach($attrs as $key=>$value) {
			$attr_pairs[] = "`{$key}`='{$value}'";
		}
		$sql = "UPDATE ".self::table_name()." SET ";
		$sql .= join(", ", $attr_pairs);
		$sql .= " WHERE id=".self::db()->prepare_value($this->id);
		return (self::db()->execute($sql) == 1)? true : false ;
	}
	
	public function delete() {
		$sql = "DELETE FROM ".self::table_name();
		$sql .= " WHERE id=".self::db()->prepare_value($this->id);
		$sql .= " LIMIT 1";
		self::db()->execute($sql);
		return (self::db()->affected_rows() == 1)? true : false ;
	}	
}

?>