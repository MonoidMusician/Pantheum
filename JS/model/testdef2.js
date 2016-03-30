var connection = require('./mysql');
var model = require('./definition');

connection.connect();

function _promiseUntil(condition, action, value) {
	return condition(value).then(function(c) {
		if (!c) {
			return action(value).then(_promiseUntil.bind(null, condition, action));
		} else return Promise.resolve(value);
	});
}
function promiseUntil(value, condition, action) {
	return _promiseUntil(condition, action, value);
}
// Bind the fn as a Python function, where there first argument is used as this
function pybind(fn) {
	return function(ref) {
		return fn.call(ref);
	}
}

var d = {
	id: 15902,
	value: "am",
};
var D = new model.Definition(d);
var promise = promiseUntil(D, pybind(D.exists), function(a) {
	a.id += 1;
	return Promise.resolve(a);
}).then(function(a) {
	console.log(a);
	return D.pull();
}).then(function(a) {
	console.log("then");
	console.log(a);
	D.tag = "indicative/active/present/singular/person-1";
	D.lang = "en";
	delete d.id;
	D.fromData(d);
	return D.push();
}).then(function(a) {
	console.log("then2");
	console.log(a);
	return D.push_id(D.id - 1);
}).then(function(a) {
	console.log("then3");
	console.log(a);
	return D.pull();/**/
}).then(function(a) {
	console.log("then4");
	console.log(a);
	connection.end();
}).catch(function(a) {
	console.log("catch");
	console.error(a.stack);
	connection.end();
});
