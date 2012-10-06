<?php

class Router {
	private static $routes = array();

	public function __construct($routes = false) {
		if(is_array($routes)) self::$routes = $routes;
		else require_once(GRIFFIN_WEBAPP.'/config/routes.php');
	}

	public static function connect($route_key, $route_params = array(), $conditions = array()) {
		self::$routes[$route_key] = $route_params;
	}

	public static function reset() {
		self::$routes = array();
	}

	public function parse($path) {
		$params = array();
		foreach(self::$routes as $route_key => $route_params) {
			$regex_route = preg_replace('/:(\w+)/', '(\w+)', $route_key);
			$regex_route = '/^'.str_replace('/', '\/', $regex_route).'$/';
			if(preg_match($regex_route, $path, $path_components)) {
				array_shift($path_components);
				preg_match_all('/:(\w+)/', $route_key, $place_holders);
				$place_holders = $place_holders[1];
				foreach ($place_holders as $index => $value) $params[$value] = $path_components[$index];
				foreach ($route_params as $index => $value) $params[$index] = $value;
				break;
			}
		}

		if(!isset($params['controller'])) $params['controller'] = 'home';
		if(!isset($params['action'])) $params['action'] = 'index';

		return $params;
	}
	
	public function route() {
		$uri = explode('?', $_SERVER['REQUEST_URI']);

		$path = trim(mb_strtolower($uri[0]), '/');
		$query_string = isset($uri[1]) ? $uri[1] : '';

		$params = $this->parse($path);

		$controller_parts = explode('_', $params['controller']);
		foreach ($controller_parts as $key => $value) {
			$controller_parts[$key] = ucfirst($value);
		}
		$controller_name = implode('', $controller_parts).'Controller';
		try {
			$controller = new $controller_name($params);
			$controller->process();
		}
		catch(Exception $e) {
			echo "$path - Not Found(".$e->getMessage().") <hr />";
			print_r($params);
		}
	}
}

?>