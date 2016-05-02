<?php

namespace dice;

abstract class DefaultScope
{
    /**
     * Find lowest value
     *
     * @param $left BaseExpression
     * @param $right BaseExpression
     * @param $scope Scope
     *
     * @return int
     * @throws \TypeError
     */
    public function min($left, $right, $scope) {
        $left = $left->evaluate($scope);
        if (!is_numeric($left->value)) {
            throw new \TypeError('Non-number passed to "min()": ' . $left->value);
        }

        $right = $right->evaluate($scope);
        if (!is_numeric($right->value)) {
            throw new \TypeError('Non-number passed to "min()": ' . $right->value);
        }

        return min($left->value, $right->value);
    }

    /**
     * Find highest value
     *
     * @param $left BaseExpression
     * @param $right BaseExpression
     * @param $scope Scope
     *
     * @return int
     * @throws \TypeError
     */
    public function max($left, $right, $scope) {
        $left = $left->evaluate($scope);
        if (!is_numeric($left->value)) {
            throw new \TypeError('Non-number passed to "max()": ' . $left->value);
        }

        $right = $right->evaluate($scope);
        if (!is_numeric($right->value)) {
            throw new \TypeError('Non-number passed to "max()": ' . $right->value);
        }

        return max($left->value, $right->value);
    }

    /**
     * Round fractions down
     *
     * @param $expr BaseExpression
     * @param $scope Scope
     *
     * @return int
     * @throws \TypeError
     */
    public function floor($expr, $scope) {
        $expr = $expr->evaluate($scope);
        if (!is_numeric($expr->value)) {
            throw new \TypeError('Non-number passed to "floor()": ' . $expr->value);
        }

        return floor($expr->value);
    }

    /**
     * Round fractions up
     *
     * @param $expr BaseExpression
     * @param $scope Scope
     *
     * @return int
     * @throws \TypeError
     */
    public function ceil($expr, $scope) {
        $expr = $expr->evaluate($scope);
        if (!is_numeric($expr->value)) {
            throw new \TypeError('Non-number passed to "ceil()": ' . $expr->value);
        }

        return ceil($expr->value);
    }

    /**
     * Rounds a number
     *
     * @param $expr BaseExpression
     * @param $scope Scope
     *
     * @return int
     * @throws \TypeError
     */
    public function round($expr, $scope) {
        $expr = $expr->evaluate($scope);
        if (!is_numeric($expr->value)) {
            throw new \TypeError('Non-number passed to "round()": ' . $expr->value);
        }

        return round($expr->value);
    }
}
