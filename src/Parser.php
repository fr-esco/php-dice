<?php

namespace dice;

class Parser extends BaseParser
{

}

function rollDie($sides) {
    if (function_exists('random_int')) {
        return random_int(1, $sides);
    } else {
        return mt_rand(1, $sides);
    }
}

class Roll extends BaseExpression
{
    public $type = 'roll';
    public $results = [];

    /** @var int */
    private $count;

    /** @var int */
    private $sides;

    public function __construct($count, $sides) {
        $this->count = $count === null || !is_numeric($count) ? 1 : intval($count);
        $this->sides = $sides;
    }

    public function evaluate($scope = null) {
        for ($i = 0; $i < $this->count; $i++)
            $this->results[] = rollDie($this->sides);
        $this->value = array_reduce($this->results, function ($sum, $result) {
            return $sum + $result;
        }, 0);

        return $this;
    }

    public function render() {
        $roll = implode('', [$this->count, 'd', $this->sides]);
        $results = implode(', ', $this->results);

        return implode(' ', ['{', $roll, ': [', $results, '] }']);
    }

    public function __toString() {
        return implode('', [$this->count, 'd', $this->sides]);
    }
}

class Operation extends BaseExpression
{
    private static $typeToString = [
        'add' => '+',
        'subtract' => '-',
        'multiply' => '*',
        'divide' => '/',
    ];

    /** @var BaseExpression */
    private $left;

    /** @var BaseExpression */
    private $right;

    public function __construct($type, $left, $right) {
        $this->type = $type;
        $this->left = $left;
        $this->right = $right;
    }

    public function evaluate($scope = null) {
        $scope = new Scope($scope);

        $this->left = $this->left->evaluate($scope);
        $this->right = $this->right->evaluate($scope);

        switch ($this->type) {
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

    public function render() {
        $op = self::$typeToString[$this->type];

        return implode(' ', [$this->left->render(), $op, $this->right->render()]);
    }

    public function __toString() {
        $op = self::$typeToString[$this->type];

        return implode(' ', [$this->left, $op, $this->right]);
    }
}

class Num extends BaseExpression
{
    public $type = 'number';

    public function __construct($value) {
        $this->value = $value;
    }

    public function evaluate($scope = null) {
        return $this;
    }

    public function render() {
        return strval($this->value);
    }

    public function __toString() {
        return strval($this->value);
    }
}

class Variable extends BaseExpression
{
    public $type = 'variable';

    /** @var string */
    private $name;

    public function __construct($name) {
        $this->name = $name;
    }

    public function evaluate($scope = null) {
        $scope = new Scope($scope);
        $this->value = $scope->{$this->name};

        return $this;
    }

    public function render() {
        $name = $this->needsEscaping() ? "'" . $this->name . "'" : $this->name;

        return implode(' ', ['{', $name, ':', $this->value, '}']);
    }

    public function __toString() {
        return $this->needsEscaping() ? "'" . $this->name . "'" : $this->name;
    }

    private function needsEscaping() {
        return preg_match('/^[A-Za-z_][A-Za-z0-9_]+$/', $this->name) !== 1;
    }
}

class Func extends BaseExpression
{
    public $type = 'function';
    public $results = [];

    /** @var string */
    private $name;

    /** @var array */
    private $args;

    public function __construct($name, $args = []) {
        $this->name = $name;
        $this->args = $args;
    }

    public function evaluate($scope = null) {
        $scope = new Scope($scope);

        $this->value = call_user_func_array([$scope, $this->name], array_merge($this->args, [$scope]));

        return $this;
    }

    public function render() {
        $name = $this->needsEscaping() ? "'" . $this->name . "'" : $this->name;
        $args = array_reduce($this->args, function ($result, $arg) {
            /** @var $arg BaseExpression */
            if ($arg instanceOf BaseExpression)
                $result[] = $arg->render();

            return $result;
        }, []);

        return implode(' ', ['{', $name, '(', implode(', ', $args), '):', $this->value, '}']);
    }

    public function __toString() {
        $name = $this->needsEscaping() ? "'" . $this->name . "'" : $this->name;

        return implode('', [$name, '(', implode(', ', $this->args), ')']);
    }

    private function needsEscaping() {
        return preg_match('/^[A-Za-z_][A-Za-z0-9_]+$/', $this->name) !== 1;
    }
}

class Repeat extends BaseExpression
{
    public $type = 'repeat';
    public $results = [];

    /** @var int */
    private $count;

    /** @var BaseExpression */
    private $right;

    public function __construct($count, $right) {
        $this->count = $count;
        $this->right = $right;
    }

    public function evaluate($scope = null) {
        $scope = new Scope($scope);

        for ($i = 0; $i < $this->count; $i++)
            $this->results[] = $this->right->evaluate($scope);
        $this->value = array_reduce($this->results, function ($sum, $result) {
            return $sum + $result->value;
        }, 0);

        return $this;
    }

    public function render() {
        return implode('', [$this->count, '(', $this->right->render(), ')']);
    }

    public function __toString() {
        return implode('', [$this->count, '(', $this->right, ')']);
    }
}
