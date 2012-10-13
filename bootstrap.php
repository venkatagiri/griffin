<?php

define('GRIFFIN_ROOT', dirname(__FILE__));
if(getenv('TEST') == null) define('GRIFFIN_WEBAPP', GRIFFIN_ROOT.'/..');
else define('GRIFFIN_WEBAPP', GRIFFIN_ROOT.'/tests/test_app');

require_once(GRIFFIN_ROOT.'/core/class.Loader.php'); #Lazy Loader
Loader::register();

if(getenv('TEST') == null) {
	$router = new Router(GRIFFIN_WEBAPP.'/config/routes.php');
	$router->route();
}

?>