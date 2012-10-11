<?php

class Config {
	private static $_instance;
	private $_config = array();

	private function __construct() {
		require_once(GRIFFIN_ROOT.'/core/config.php');
		if(getenv('TEST') == null) {
			require_once(GRIFFIN_WEBAPP.'/config/environment.php');
			require_once(GRIFFIN_WEBAPP.'/config/database.php');
		} else {
			require_once(GRIFFIN_ROOT.'/tests/config.tests.php');
		}
		$this->_config = $GRIFFIN_CFG;
	}

	public static function get_instance() {
		if(!self::$_instance) self::$_instance = new Config();
		return self::$_instance;
	}

	public function get($key) {
		if(isset($this->_config[$key])) return $this->_config[$key];
		return null;
	}
}

?>