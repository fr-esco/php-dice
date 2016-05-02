<?php

require __DIR__ . '/vendor/autoload.php';

$input = 'd6';

try {
	$parser = new dice\Parser;
	$result = $parser->parse($input);
	
	echo '<pre>';
	print_r($result->evaluate());
	echo '</pre>';
} catch (dice\SyntaxError $ex) {
    $message = 'Syntax error: ' . $ex->getMessage() . ' At line ' . $ex->grammarLine . ' column ' . $ex->grammarColumn . ' offset ' . $ex->grammarOffset;
}
