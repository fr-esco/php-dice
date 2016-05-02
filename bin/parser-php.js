var fs = require('fs'),
	path = require('path');

var pegjs = require('pegjs');
var phppegjs = require('php-pegjs');
var grammar = fs.readFileSync('./grammar/dice.pegphp', 'utf8');
/*
console.log(grammar);
*/
var parserClassName = 'BaseParser';
var parser = pegjs.buildParser(grammar, {
	phppegjs: {
		parserNamespace: 'dice',
		parserClassName: parserClassName
	},
    plugins: [phppegjs]
});
/*
console.log(parser);
*/

fs.writeFileSync(path.join('src', parserClassName + '.php'), parser);
