<?php

class Table {
	private static $_cache;
	
	public static function get_table_name($class) {
		if(!isset(self::$_cache['table_names'][$class])) {
			self::$_cache['table_names'][$class] = Inflector::tableize($class);
		}
		return self::$_cache['table_names'][$class];
	}

	public static function add_table_name($class, $table_name) {
		self::$_cache['table_names'][$class] = $table_name;
	}

	public static function get_columns($table_name) {
		if(!isset(self::$_cache['column_names'][$table_name])) {
			$db = Database::getInstance();
			$columns = $db->query("DESCRIBE $table_name");
			$column_names = array();
			foreach($columns as $column) {
				$column_names[] = $column['Field'];
			}
			self::$_cache['column_names'][$table_name] = $column_names;
		}
		return self::$_cache['column_names'][$table_name];
	}
}

?>