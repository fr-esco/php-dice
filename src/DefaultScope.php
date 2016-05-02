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

    /**
     * Re-roll on max die result
     *
     * @param $expr Roll
     * @param $scope Scope
     *
     * @return int
     * @throws \TypeError
     */
    public function explode($expr, $scope) {
        if ($expr->type !== 'roll') {
            throw new \TypeError('Non-roll passed to "explode()": ' . $expr);
        }

        $roll = $expr->evaluate($scope);
        $expr = $roll;
        $results = [$roll->value];

        while ($roll->value === $roll->sides) {
            $roll = $expr->evaluate($scope);
            $results[] = $roll->value;
        }

        return array_reduce($results, function ($sum, $result) {
            return $sum + $result;
        }, 0);
    }

    /**
     * Drop lowest dice result
     *
     * @param $expr Roll
     * @param $scope Scope
     *
     * @return int
     * @throws \TypeError
     */
    public function dropLowest($expr, $scope) {
        if ($expr->type !== 'roll') {
            throw new \TypeError('Non-roll passed to "dropLowest()": ' . $expr);
        }

        $results = $expr->evaluate($scope)->results;

        sort($results, SORT_NUMERIC);
        array_shift($results);

        return array_reduce($results, function ($sum, $result) {
            return $sum + $result;
        }, 0);
    }

    /**
     * Drop highest dice result
     *
     * @param $expr Roll
     * @param $scope Scope
     *
     * @return int
     * @throws \TypeError
     */
    public function dropHighest($expr, $scope) {
        if ($expr->type !== 'roll') {
            throw new \TypeError('Non-roll passed to "dropHighest()": ' . $expr);
        }

        $results = $expr->evaluate($scope)->results;

        sort($results, SORT_NUMERIC);
        array_pop($results);

        return array_reduce($results, function ($sum, $result) {
            return $sum + $result;
        }, 0);
    }

    /**
     * Re-roll a die when result above the first input parameter
     *
     * @param $maxVal float
     * @param $expr Roll
     * @param $scope Scope
     *
     * @return int
     * @throws \TypeError
     */
    public function rerollAbove($maxVal, $expr, $scope) {
        $roll = $expr->evaluate($scope);
        if ($expr->type !== 'roll') {
            throw new \TypeError('Non-roll passed to "rerollAbove()": ' . $expr);
        }

        $maxVal = $maxVal->evaluate($scope);
        if (!is_numeric($maxVal->value)) {
            throw new \TypeError('Non-number passed to "rerollAbove()": ' . $maxVal->value);
        }

        $results = array_reduce($roll->results, function ($results, $result) use ($maxVal, $roll) {
            if ($result >= $maxVal->value) {
                // We reroll, and keep
                $results[] = rollDie($roll->sides);
            } else {
                $results[] = $result;
            }

            return $results;
        }, []);

        return array_reduce($results, function ($sum, $result) {
            return $sum + $result;
        }, 0);
    }

    /**
     * Re-roll a die when result below the first input parameter
     *
     * @param $minVal float
     * @param $expr Roll
     * @param $scope Scope
     *
     * @return int
     * @throws \TypeError
     */
    public function rerollBelow($minVal, $expr, $scope) {
        $roll = $expr->evaluate($scope);
        if ($expr->type !== 'roll') {
            throw new \TypeError('Non-roll passed to "rerollBelow()": ' . $expr);
        }

        $minVal = $minVal->evaluate($scope);
        if (!is_numeric($minVal->value)) {
            throw new \TypeError('Non-number passed to "rerollBelow()": ' . $minVal->value);
        }

        $results = array_reduce($roll->results, function ($results, $result) use ($minVal, $roll) {
            if ($result <= $minVal->value) {
                // We reroll, and keep
                $results[] = rollDie($roll->sides);
            } else {
                $results[] = $result;
            }

            return $results;
        }, []);

        return array_reduce($results, function ($sum, $result) {
            return $sum + $result;
        }, 0);
    }
}
