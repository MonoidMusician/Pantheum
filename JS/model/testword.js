var connection = require('./mysql');
var model = require('./model');

connection.connect();

var W = model.Word({id: 10176}, true);
console.log(W);
var promise = W.pull().then(function(a) {
	console.log("then");
	console.log(a);
	return W.pullchildren();
}).then(function(a) {
	console.log("then2");
	W.definitions.push(model.Definition({
		value: "ser, estar",
		lang: "es",
		word: W,
	}, true));
	console.log(a.definitions);
	return W.update();
}).then(function(a) {
	console.log("then3");
	console.log(a.definitions);
	W.definitions.length = 2;
	return W.update();
}).then(function(a) {
	console.log("then4");
	console.log(a.toData());
	console.log(a.toData().definitions);
	connection.end();
}).catch(function(a) {
	console.log("catch");
	console.error(a.stack);
	connection.end();
});
