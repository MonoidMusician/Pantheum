var connection = require('./mysql');
var model = require('./definition');

connection.connect();
connection.query('SHOW CREATE TABLE definitions', function(err, rows, fields) {
	if (err) throw err;

	//console.log(rows[0]["Create Table"]);
});

var d = {
	word: {id:10176},
	lang: "en",
	value: "be",
};
var D = new model.Definition(d);
var promise = D.create().then(function(a) {
	console.log("then");
	console.log(a);
}).catch(function(a) {
	console.log("catch");
	console.log(a);
});
connection.end();
