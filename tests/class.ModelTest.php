<?php

class ModelTest extends UnitTestCase {
	protected $model;

	public function setUp() {
		$this->model = new Product();
	}

	public function testCount() {
		$this->assertEqual(0, Product::count());
	}
}

?>