<?php

class ControllerTest extends UnitTestCase {
	public function testProcess() {
		$params = array(
			'controller' => 'products',
			'action' => 'index'
		);
		$controller = new ProductsController($params);
		$output = $controller->process(true);

		$this->assertEqual('56', $output);
    }
}

?>