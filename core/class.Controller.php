<?php

abstract class Controller {
	private $_data = array();
	private $_is_rendered = false;
	public $params = array();
	
	function __construct($params = array()) {
		$this->params = $params;
	}

	function __get($name) {
		if(isset($this->_data[$name])) return $this->_data[$name];
		return false;
	}

	function __set($name, $value) {
		$this->_data[$name] = $value;
	}

	function __isset($name) {
		return isset($this->_data[$name]);
	}

	public function process($render_to_string = false) {
		$action = $this->params['action'];
		if(!method_exists($this, $action)) throw new Exception('404');
		$this->$action(); // Execute the action.
		if(!$this->_is_rendered) return $this->render($action, $render_to_string); // Render the action's view.
	}

	public function render_to_string($args = false) {
		return $this->render($args, true);
	}

	protected function render($args = false, $render_to_string = false) {
		if(is_string($args)) {
			if(strpos($args, '/') === false) {
				// Rendering a view in the current Controller.
				list($controller, $view) = array($this->params['controller'], $args);
			} else if(substr_count($args, '/') === 1) {
				// Rendering a view from another Controller.
				list($controller, $view) = explode('/', $args);
			} else {
				throw new Exception('Render - Invalid Arguments!');
			}
		} else if(is_array($args)) {
			// Render by parsing the array of options.
			$controller = isset($args['controller']) ? $args['controller'] : $this->params['controller'];
			$view = isset($args['action']) ? $args['action'] : $this->params['action'];
		} else {
			throw new Exception('Render - Invalid Arguments!');
		}
		return $this->render_view($controller, $view, $render_to_string);
	}

	private function render_view($controller, $view, $render_to_string = false, $params = array()) {
		if($this->_is_rendered) throw new Exception('Can only render once per action');

		$view_file = GRIFFIN_WEBAPP.'/app/views/'.$controller.'/'.$view.'.php';
		if(!file_exists($view_file)) throw new Exception('404');

		if($render_to_string) {
			ob_start();
			include($view_file);
			return ob_get_clean();
		} else require_once($view_file);
		$this->_is_rendered = true;
	}
}

?>