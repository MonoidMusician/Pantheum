var connection = require('./mysql');
var model = Object.assign({}, require('./definition'), require('./word'));

connection.connect();

var w = {
	id: 10176,
};
var W = new model.Word(w);
var promise = W.pull().then(function(a) {
	console.log("then");
	console.log(a);
	return W.pullchildren();
}).then(function(a) {
	console.log("then2");
	console.log(a.definitions);
	W.definitions.push(new model.Definition({
		value: "ser, estar",
		lang: "es",
		word: W,
	}));
	return W.update();
}).then(function(a) {
	console.log("then2");
	console.log(a.definitions);
	W.definitions.length = 2;
	return W.update();
}).then(function(a) {
	console.log("then3");
	console.log(a);
	connection.end();
}).catch(function(a) {
	console.log("catch");
	console.error(a.stack);
	connection.end();
});
