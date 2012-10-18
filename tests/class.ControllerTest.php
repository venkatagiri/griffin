<?php

class ControllerTest extends UnitTestCase {
	private $products_controller;

	public function setUp() {
		$params = array(
			'controller' => 'products',
			'action' => 'index'
		);
		$this->products_controller = new ProductsController($params);
	}

	public function testProcess() {
		$params = array(
			'controller' => 'products',
			'action' => 'index',
			'result' => '56'
		);
		$controller = new ProductsController($params);
		$output = $controller->process(true);

		$this->assertEqual('56', $output);
	}

	public function testProcess_InvalidActionName() {
		$params = array(
			'controller' => 'products',
			'action' => 'invalid_action'
		);
		$controller = new ProductsController($params);

		$this->expectException(new Exception('Invalid action name!'));
		$controller->process();
	}

	public function testRender_ViewNameAsArgument() {
		$this->assertEqual('Test the Render method.', $this->products_controller->render('render', true));
	}

	public function testRender_ViewOfDifferentController() {
		$this->assertEqual('The Orders Page.', $this->products_controller->render('orders/index', true));
	}

	public function testRender_ArrayAsArguments() {
		$args = array(
			'controller' => 'orders',
			'action' => 'index'
		);
		$this->assertEqual('The Orders Page.', $this->products_controller->render($args, true));
	}

	public function testRender_RenderOncePerAction() {
		$this->expectException(new Exception('Can only render once per action!'));
		$this->products_controller->render('index');
		$this->products_controller->render('orders/index'); // Will throw an exception
	}

	public function testRender_InvalidArguments_EmptyArgument() {
		$this->expectException(new Exception('Render - Invalid Arguments!'));
		$this->products_controller->render('');
	}

	public function testRender_InvalidArguments_IntegerForString() {
		$this->expectException(new Exception('Render - Invalid Arguments!'));
		$this->products_controller->render(1);
	}

	public function testRender_InvalidView() {
		$this->expectException(new Exception('View(products/invalid_view) Not Found!'));
		$this->products_controller->render('invalid_view');
	}
}

?>