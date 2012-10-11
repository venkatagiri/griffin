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
	
	public function process() {
		$action = $this->params['action'];
		if(!method_exists($this, $action)) throw new Exception('404');
		$this->$action(); // Execute the action.
		if(!$this->_is_rendered) $this->render($action); // Render the action's view.
	}

	protected function render($args = '') {
		if(is_string($args)) {
			if(strpos($args, '/') === false) {
				// Rendering a view in the current Controller.
				$this->render_view($this->params['controller'], $args);
			} else if(substr_count($args, '/') === 1) {
				// Rendering a view from another Controller.
				list($controller, $view) = explode('/', $args);
				$this->render_view($controller, $view);
			}
		} else if(is_array($args)) {
			// Render by parsing the array of options.
			$controller = isset($args['controller']) ? $args['controller'] : $this->params['controller'];
			$view = isset($args['action']) ? $args['action'] : $this->params['action'];

			$this->render_view($controller, $view);
		}
	}

	private function render_view($controller, $view, $params = array()) {
		if($this->_is_rendered) throw new Exception('Can only render once per action');

		$view_file = GRIFFIN_WEBAPP.'/app/views/'.$controller.'/'.$view.'.php';
		if(!file_exists($view_file)) throw new Exception('404');

		require_once($view_file);
		$this->_is_rendered = true;
	}
}

?>