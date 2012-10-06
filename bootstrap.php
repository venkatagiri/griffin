<?php

define('GRIFFIN_ROOT', dirname(__FILE__));
define('GRIFFIN_WEBAPP', GRIFFIN_ROOT.'/..');

require_once(GRIFFIN_ROOT.'/core/class.Loader.php'); #Lazy Loader
Loader::register();

if(getenv('TEST') == null) {
	$router = new Router(GRIFFIN_WEBAPP.'/config/routes.php');
	$router->route();
}

?>