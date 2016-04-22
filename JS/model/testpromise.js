var connection = require('./mysql');
var Promise = require('bluebird');

connection.connect();
var promise = new Promise(function(resolve, reject) {
	connection.query('SHOW CREATE TABLE definitions', function(err, rows, fields) {
		if (err) reject(err);
		else resolve(rows[0]["Create Table"]);
	});
});
promise.then(function(a) {
	console.log("then");
	console.log(a);
});
promise.catch(function(a) {
	console.log("catch");
	console.log(a);
});
console.log(promise);
connection.end();
