<?php

namespace dice;

abstract class BaseExpression
{
    public $type = 'expression';
    public $value = null;

    abstract public function evaluate($scope = []);

    abstract public function render();
}
