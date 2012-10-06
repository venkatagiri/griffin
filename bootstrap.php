<?php

define('GRIFFIN_ROOT', dirname(__FILE__));
define('GRIFFIN_WEBAPP', GRIFFIN_ROOT.'/../griffin_sample_app');

require_once(GRIFFIN_ROOT.'/core/class.Loader.php'); #Lazy Loader
Loader::register();

if(getenv('TEST') == null) {
	$router = new Router();
	$router->route();
}

?>