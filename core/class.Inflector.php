<?php

class Inflector {
	public static function tableize($class) {
		return strtolower(
			str_replace('-', '_', 
				preg_replace('/([a-z\d])([A-Z])/', '$1_$2', 
					preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1_$2', 
						preg_replace('/::/', '/', $class)
					)
				)
			)
		).'s';
	}
}

?>