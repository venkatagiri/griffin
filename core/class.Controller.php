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
		if(!method_exists($this, $action)) throw new Exception('Invalid action name!');
		$this->$action(); // Execute the action.
		if(!$this->_is_rendered) return $this->render($action, $render_to_string); // Render the action's view.
	}

	public function render_to_string($options = false) {
		return $this->render($options, true);
	}

	public function render($options = false, $render_to_string = false) {
		if(is_string($options)) {
			if(strpos($options, '/') === false && $options != '') {
				// Rendering a view in the current Controller.
				list($controller, $view) = array($this->params['controller'], $options);
			} else if(substr_count($options, '/') === 1) {
				// Rendering a view from another Controller.
				list($controller, $view) = explode('/', $options);
			} else {
				throw new Exception('Render - Invalid Arguments!');
			}
		} else if(is_array($options)) {
			// Render by parsing the array of options.
			$controller = isset($options['controller']) ? $options['controller'] : $this->params['controller'];
			$view = isset($options['action']) ? $options['action'] : $this->params['action'];
		} else {
			throw new Exception('Render - Invalid Arguments!');
		}
		return $this->_render_view($controller, $view, $render_to_string);
	}

	public function redirect_to($options = false) {
		$location = '';
		if(is_string($options)) {
			$location = $options;
		} else if(is_array($options)) {
			$location = $this->url_for($options);
		} else {
			throw new Exception('Redirect - Invalid Arguments!');
		}
		$this->_redirect_to($location);
	}

	public function url_for($options = []) {
		if(!isset($options['controller'])) $options['controller'] = $this->params['controller'];
		if(!isset($options['action'])) $options['action'] = 'index';

		$url = $options['controller'].'/'.$options['action'];
		if(isset($options['id'])) $url .= '/'.$options['id'];
		return $url;
	}

	private function _redirect_to($location) {
		header("Location: {$location}");
		exit;
	}

	private function _render_view($controller, $view, $render_to_string = false, $params = array()) {
		if($this->_is_rendered == true) throw new Exception('Can only render once per action!');

		$view_file = GRIFFIN_WEBAPP.'/app/views/'.$controller.'/'.$view.'.php';
		if(!file_exists($view_file)) throw new Exception("View($controller/$view) Not Found!");

		$result = false;
		if($render_to_string) {
			ob_start();
			extract($this->_data);
			include($view_file);
			$result = ob_get_clean();
		} else {
			extract($this->_data);
			include($view_file);
			$result = true;
		}
		$this->_is_rendered = true;
		return $result;
	}
}

?>