<?php

class RouterTest extends UnitTestCase {
	public function setUp() {
		Router::reset();
	}
	public function testRouter() {
		Router::connect('recipes/:ingredient', array('controller' => 'recipes', 'action' => 'show'));

		$router = new Router();
		$params = $router->parse('recipes/flour');
		$this->assertEqual('recipes', $params['controller']);
		$this->assertEqual('show', $params['action']);
		$this->assertEqual('flour', $params['ingredient']);
	}

	public function testPriority() {
		Router::connect('recipes/:action', array('controller' => 'recipes_ctl'));
		Router::connect(':controller/:action/:id');

		$router = new Router();
		$params = $router->parse('recipes/list');
		$this->assertEqual('recipes_ctl', $params['controller']);
		$this->assertEqual('list', $params['action']);
	}

	public function testMatching() {
		Router::connect('admin/:controller/:action/:id');
		Router::connect('admin/:controller/:action');
		Router::connect('admin/:controller');
		Router::connect('admin', array('controller' => 'dashboard'));
		Router::connect(':controller/:action/:id');

		$router = new Router();
		$params = $router->parse('admin/product/show/1');
		$this->assertEqual('product', $params['controller']);
		$this->assertEqual('show', $params['action']);

		$params = $router->parse('admin/product/list');
		$this->assertEqual('product', $params['controller']);
		$this->assertEqual('list', $params['action']);

		$params = $router->parse('admin/product');
		$this->assertEqual('product', $params['controller']);
		$this->assertEqual('index', $params['action']);

		$params = $router->parse('admin');
		$this->assertEqual('dashboard', $params['controller']);
		$this->assertEqual('index', $params['action']);
	}
}

?>