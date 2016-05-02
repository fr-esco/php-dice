<?php

namespace dice;

class Parser extends BaseParser {
	
}

function rollDie($sides) {
	if (function_exists('random_int')) {
		return random_int(1, $sides);
	} else {
		return mt_rand(1, $sides);
	}
}
class Roll {
	public $type = 'roll';
	public $results = [];
	public function __construct($count, $sides) {
		$this->count = $count === null || !is_numeric($count) ? 1 : intval($count);
		$this->sides = $sides;
	}
	public function evaluate() {
		for ($i = 0; $i < $this->count; $i++)
			$this->results[] = rollDie($this->sides);
		$this->value = array_reduce($this->results, function($sum, $result) {
			return $sum + $result;
		}, 0);
		return $this;
	}
}
class Operation {
	public function __construct($type, $left, $right) {
		$this->type = $type;
		$this->left = $left;
		$this->right = $right;
	}
	public function evaluate($scope) {
		// TODO $scope = defaultScope.buildDefaultScope($scope);

		$this->left = $this->left->evaluate($scope);
		$this->right = $this->right->evaluate($scope);

		switch($this->type) {
			case 'add':
				$this->value = $this->left->value + $this->right->value;
				break;
			case 'subtract':
				$this->value = $this->left->value - $this->right->value;
				break;
			case 'multiply':
				$this->value = $this->left->value * $this->right->value;
				break;
			case 'divide':
				$this->value = $this->left->value / $this->right->value;
				break;
		}
		return $this;
	}
}
class Num {
	public $type = 'number';
	public function __construct($value) {
		$this->value = $value;
	}
	public function evaluate() {
		return $this;
	}
}
class Variable {
	public $type = 'variable';
	public function __construct($name) {
		$this->name = $name;
	}
	public function evaluate($scope) {
		// TODO $scope = defaultScope.buildDefaultScope($scope);
		$this->value = $scope->{$this->name};
		return $this;
	}
}
class Func {
	public $type = 'function';
	public $results = [];
	public function __construct($name, $args = []) {
		$this->name = $name;
		$this->args = $args;
	}
	public function evaluate($scope) {
		// TODO $scope = defaultScope.buildDefaultScope($scope);
		$this->value = $scope->{$this->name}(array_merge($this->args, [$scope]));
		return $this;
	}
}
class Repeat {
	public $type = 'repeat';
	public $results = [];
	public function __construct($count, $right) {
		$this->count = $count;
		$this->right = $right;
	}
	public function evaluate($scope) {
		// TODO $scope = defaultScope.buildDefaultScope($scope);
		for ($i = 0; $i < $this->count; $i++)
			$this->results[] = $this->right->evaluate($scope);
		$this->value = array_reduce($this->results, function($sum, $result) {
			return $sum + $result->value;
		}, 0);
		return $this;
	}
}
