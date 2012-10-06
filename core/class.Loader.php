<?php

require_once(GRIFFIN_ROOT.'/core/class.Config.php');

class Loader {
	public static function register() {
		return spl_autoload_register(array(__CLASS__, "load"));
	}

	public static function load($class) {
		if (class_exists($class, false)) return;

		foreach(Config::get_instance()->get('classpath') as $path) {
			$file_name = $path.'/class.'.$class.'.php';
			if(file_exists($file_name)) {
				require_once($file_name);
				return;
			}
		}

		throw new Exception("Ahhhhhh! You have found my Achilles Heal! ($class)");
	}
}

?>