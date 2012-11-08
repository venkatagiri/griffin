<?php

class ProductsController extends Controller {
	public function index() {
		$this->msg = "The Index Page!";
	}

	public function extract() {
		$this->int_param = $this->params['int_param'];
		$this->array_param = $this->params['array_param'];
	}
}

?>