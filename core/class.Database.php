<?php

class Database {
	private $connection;
	private $last_query;
	private $config;
	private static $instance;

	private function __construct() {
		$this->config = Config::get_instance();
		try {
			$db_string = sprintf("mysql:host=%s;dbname=%s", $this->config->get('db_host'), $this->config->get('db_name'));
			$this->connection = new PDO($db_string,
				$this->config->get('db_user'),
				$this->config->get('db_password'),
				array(PDO::ATTR_PERSISTENT => true));
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e) {
			$error = "Oops! Something went wrong. Our coding monkeys will be summoned to fix this. Please go back and try again!";
			if($this->config->get('debug_mode')) {
				$error .= '<br />Database connection failed : ' . $e->getMessage();
			}
			die($error);
		}
	}

	function __destruct() {
		$this->connection = null;
	}

	public static function get_instance() {
		if(!self::$instance) self::$instance = new Database();
		return self::$instance;
	}

	public function execute($sql) {
		$this->last_query = $sql;
		try {
			return $this->connection->exec($sql);
		}
		catch(PDOException $e) {
			$error = "Oops! Something went wrong. Our coding monkeys will be summoned to fix this. Please go back and try again!";
			if($this->config->get('debug_mode')) {
				$error .= '<br />Database query failed : ' . $e->getMessage();
				$error .= "<br />Last Query was : " . $this->last_query;
			}
			die($error);
		}
	}

	public function query($sql, $class = "") {
		$this->last_query = $sql;
		try {
			if($class != "") return $this->connection->query($sql, PDO::FETCH_CLASS, $class);
			else return $this->connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			$error = "Oops! Something went wrong. Our coding monkeys will be summoned to fix this. Please go back and try again!";
			if($this->config->get('debug_mode')) {
				$error .= '<br />Database query failed : ' . $e->getMessage();
				$error .= "<br />Last Query was : " . $this->last_query;
			}
			die($error);
		}
	}

	public function insert_id() {
		return $this->connection->lastInsertId();
	}

	public function prepare_value($value) {
		return get_magic_quotes_gpc() ? $this->connection->quote($value): $this->connection->quote(stripslashes($value));
	}

	public function last_query() {
		return $this->last_query;
	}

	public function begin_transaction() {
		$this->connection->beginTransaction();
	}

	public function commit() {
		$this->connection->commit();
	}

	public function rollback() {
		$this->connection->rollBack();
	}	
}

?>