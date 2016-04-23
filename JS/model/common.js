"use strict";
var stampit = require('stampit');
var implementation = require('./server.js'); // require('./client.js') for the client; see package.json
var cached = require('./cached.js');
var cycle = require('cycle');

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
	if (false) {
		var get = config.get; var set = config.set;
		config.get = function() {
			console.log('trace get '+this.table+'.'+name);
			return get.call(this);
		};
		config.set = function(v) {
			console.log('trace set '+this.table+'.'+name, v);
			return set.call(this, v);
		};
	}
	config.enumerable = global_enumerable;
	Object.defineProperty(obj, name, config);
	if (value !== undefined)
		obj[name] = value;
}
function defprops(obj, names, src) {
	for (let n of names) defprop(obj, n, src);
}
// Set members as enumerable
function enumerable(obj, names) {
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

function getchildren() {
	"use strict";
	if (!this.references) return;
	var children = {};
	for (let c of this.references)
		children[c.table] = this[c.table];
	return children;
}
function setchildren(children) {
	"use strict";
	if (!this.references) return;
	for (let c of this.references) {
		if (children[c.table])
			this[c.table] = children[c.table];
	}
}
var base = stampit({
	methods: {
		toJSON(...arg) {
			return cycle.decycle(this.toData(...arg));
		},
		fromJSON(data) {
			return this.fromData(cycle.retrocycle(data));
		},
	},
	init: ({instance}) => {
		Object.defineProperty(instance, 'children', {
			enumerable: true, configurable: true,
			get: getchildren, set: setchildren,
		});
	}
});

// Compose a full model stamp
function stamp(stamp) {
	var cls = stampit.compose(SelfAware, base, implementation, stamp, cached);
	cls.fromData = function(...arg) {
		if (this === cls) return cls().fromData(...arg);
		return cls().fromData(this, ...arg);
	};
	cls.fromData.cacheable = function(cacheable) {
		if (!cacheable) return cls.fromData;
		return function(...arg) {
			if (!this || this === cls) return cls({id:arg[0].id}, true).fromData(...arg);
			return cls({id:this.id}, cacheable).fromData(this, ...arg);
		}
	};
	cls.fromJSON = function(...arg) {
		if (this === cls) return cls().fromJSON(...arg);
		return cls().fromJSON(this, ...arg);
	};
	cls.fromJSON.cacheable = function(cacheable) {
		if (!cacheable) return cls.fromData;
		return function(...arg) {
			if (!this || this === cls) return cls({id:arg[0].id}, true).fromJSON(...arg);
			return cls({id:this.id}, cacheable).fromJSON(this, ...arg);
		}
	};
	return cls;
}
// Visit
function visit(visited, obj, fn) {
	for (let [o,d] of visited)
		if (o === obj) return d;
	return fn.call(obj, visited);
}
function construct(stamp, instance, ...arg) {
	if (!stamp || (instance.getStamp && instance.getStamp() === stamp)) return instance;
	return stamp(instance, ...arg);
}
module.exports = {defprops, methods, stamp, visit, construct};
