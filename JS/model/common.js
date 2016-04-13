var stampit = require('stampit');
var server = require('./server.js');
var cached = require('./cached.js');

var global_enumerable = false;
// Define default setters/getters for [name] using this['_'+name]
function defprop(obj, name, src) {
	var _name = '_'+name;
	function get() {return this[_name];}
	function set(v) {this[_name] = v;}
	var props = Object.getOwnPropertyDescriptor(src||obj, name);
	if (src === obj && props && "get" in props && "set" in props) return;
	var config = {
		get: get, set: set,
		configurable: true,
	};
	var value = obj[name];
	if (props && !props.set) delete props.set;
	if (props && !props.get) delete props.get;
	Object.assign(config, props);
	config.enumerable = global_enumerable;
	Object.defineProperty(obj, name, config);
	if (value !== undefined)
		obj[name] = value;
}
function defprops(obj, names, src) {
	"use strict";
	for (let n of names) defprop(obj, n, src);
}
// Set members as enumerable
function enumerable(obj, names) {
	"use strict";
	for (let n of names)
		Object.defineProperty(obj, n, {enumerable:true});
	return obj;
}
// Set standard methods as enumerable
function methods(methods) {
	return enumerable(methods, ['toData','fromData','toSQL','fromSQL']);
}
var SelfAware = stampit.init(({ instance, stamp }) => {
	if (!stamp.fixed.methods.getStamp) { // Avoid adding the same method to the prototype twice.
		stamp.fixed.methods.getStamp = () => stamp;
	}
});
// Compose a full model stamp
function stamp(stamp) {
	return stampit.compose(SelfAware, server, stamp, cached);
}
// Visit
function visit(visited, obj, fn) {
	"use strict";
	for (let [o,d] of visited) {
		if (o === obj) return d;
	}
	return fn.call(obj, visited);
}
function construct(stamp, instance, ...arg) {
	if (!stamp || instance.getStamp() === stamp) return instance;
	return stamp(instance, ...arg);
}
module.exports = {defprops, methods, stamp, visit, construct};
