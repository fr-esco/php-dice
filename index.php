<?php

require __DIR__ . '/vendor/autoload.php';

$expression = isset($_GET['expression']) ? $_GET['expression'] : 'd6 + foo * bar() / defaultSides + min(d12, 2d4) + rerollBelow(5, 3d6)';
$expression = preg_replace('/\s{2,}/', ' ', $expression);
?>

    <form>
        <input type="text" name="expression" value="<?= $expression ?>" style="width: 85%;">
        <input type="submit" value="Roll" style="width: 10%;">
    </form>

<?php

try {
    $parser = new dice\Parser;
    $result = $parser->parse($expression, [
        'foo' => 2,
        'bar' => function () {
            return 3;
        },
    ]);
    $evaluation = $result->evaluate();

    echo '<h2>Render</h2>', '<pre>';
    echo $result->render(), ' = ', $result->value;
    echo '</pre>';
    echo '<h2>toString</h2>', '<pre>';
    echo $result, ' = ', $result->value;
    echo '</pre>';
    echo '<h2>Evaluate</h2>', '<pre>';
    print_r($evaluation);
    echo '</pre>';
} catch (dice\SyntaxError $ex) {
    $stack = ['Syntax error:', $ex->getMessage(), 'At line', $ex->grammarLine, 'column', $ex->grammarColumn, 'offset', $ex->grammarOffset];
    echo implode(' ', $stack);
}
