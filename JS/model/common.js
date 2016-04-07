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
	if (props) {
		delete props.value;
		delete props.writable;
	}
	if (props && !props.set) delete props.set;
	if (props && !props.get) delete props.get;
	Object.assign(config, props);
	config.enumerable = global_enumerable;
	Object.defineProperty(obj, name, config);
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
// Compose a full model stamp
function stamp(stamp) {
	return stampit.compose(server, stamp, cached);
}
module.exports = {defprops, methods, stamp};
