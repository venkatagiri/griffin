<?php

class Config {
	private static $instance;
	private $config = array();

	private function __construct() {
		require_once(GRIFFIN_ROOT.'/core/config.php');
		if(getenv('TEST') == null) {
			require_once(GRIFFIN_WEBAPP.'/config/environment.php');
			require_once(GRIFFIN_WEBAPP.'/config/database.php');
		} else {
			require_once(GRIFFIN_ROOT.'/tests/config.tests.php');
		}
		$this->config = $GRIFFIN_CFG;
	}

	public static function get_instance() {
		if(!self::$instance) self::$instance = new Config();
		return self::$instance;
	}

	public function get($key) {
		if(isset($this->config[$key])) return $this->config[$key];
		return null;
	}
}

?>