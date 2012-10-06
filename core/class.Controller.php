<?php

abstract class Controller {
	public $params = array();
	public $data = array();
	protected $_db;
	
	function __construct($params = array()) {
		$this->_db = Database::get_instance();
		$this->params = $params;
	}

	function __get($name) {
		if(isset($this->data[$name])) return $this->data[$name];
		return false;
	}

	function __set($name, $value) {
		$this->data[$name] = $value;
	}
	
	public function process() {
		if(!method_exists($this, $this->params['action'])) $action = 'index';
		else $action = $this->params['action'];
		$this->$action();
		include(GRIFFIN_WEBAPP.'/app/views/'.$this->params['controller'].'/'.$action.'.php');
	}
}

?>