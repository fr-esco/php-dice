<?php

require __DIR__ . '/vendor/autoload.php';

$expression = isset($_GET['expression']) ? $_GET['expression'] : 'd6';
$expression = preg_replace('/\s{2,}/', ' ', $expression);
?>

<form>
	<input type="text" name="expression" value="<?= $expression ?>">
	<input type="submit" value="go">
</form>

<?php

try {
	$parser = new dice\Parser;
	$result = $parser->parse($expression);
	
	echo '<h2>Evaluate</h2>', '<pre>';
	print_r($result->evaluate());
	echo '</pre>';
	echo '<h2>Render</h2>', '<pre>';
	echo $result->render(), ' = ', $result->value;
	echo '</pre>';
	echo '<h2>toString</h2>', '<pre>';
	echo $result, ' = ', $result->value;
	echo '</pre>';
} catch (dice\SyntaxError $ex) {
    $message = 'Syntax error: ' . $ex->getMessage() . ' At line ' . $ex->grammarLine . ' column ' . $ex->grammarColumn . ' offset ' . $ex->grammarOffset;
}
