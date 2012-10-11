<?php

class Database {
	private $_connection;
	private $_last_query;
	private $_config;
	private static $_instance;

	private function __construct() {
		$this->_config = Config::get_instance();
		try {
			$db_string = sprintf("mysql:host=%s;dbname=%s", $this->_config->get('db_host'), $this->_config->get('db_name'));
			$this->_connection = new PDO($db_string,
				$this->_config->get('db_user'),
				$this->_config->get('db_password'),
				array(PDO::ATTR_PERSISTENT => true));
			$this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e) {
			$error = "Oops! Something went wrong. Our coding monkeys will be summoned to fix this. Please go back and try again!";
			if($this->_config->get('debug_mode')) {
				$error .= '<br />Database connection failed : ' . $e->getMessage();
			}
			die($error);
		}
	}

	function __destruct() {
		$this->_connection = null;
	}

	public static function get_instance() {
		if(!self::$_instance) self::$_instance = new Database();
		return self::$_instance;
	}

	public function execute($sql) {
		$this->_last_query = $sql;
		try {
			return $this->_connection->exec($sql);
		}
		catch(PDOException $e) {
			$error = "Oops! Something went wrong. Our coding monkeys will be summoned to fix this. Please go back and try again!";
			if($this->_config->get('debug_mode')) {
				$error .= '<br />Database query failed : ' . $e->getMessage();
				$error .= "<br />Last Query was : " . $this->_last_query;
			}
			die($error);
		}
	}

	public function query($sql, $class = "") {
		$this->_last_query = $sql;
		try {
			if($class != "") return $this->_connection->query($sql, PDO::FETCH_CLASS, $class);
			else return $this->_connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			$error = "Oops! Something went wrong. Our coding monkeys will be summoned to fix this. Please go back and try again!";
			if($this->_config->get('debug_mode')) {
				$error .= '<br />Database query failed : ' . $e->getMessage();
				$error .= "<br />Last Query was : " . $this->_last_query;
			}
			die($error);
		}
	}

	public function insert_id() {
		return $this->_connection->lastInsertId();
	}

	public function prepare_value($value) {
		return get_magic_quotes_gpc() ? $this->_connection->quote($value): $this->_connection->quote(stripslashes($value));
	}

	public function last_query() {
		return $this->_last_query;
	}

	public function begin_transaction() {
		$this->_connection->beginTransaction();
	}

	public function commit() {
		$this->_connection->commit();
	}

	public function rollback() {
		$this->_connection->rollBack();
	}	
}

?>