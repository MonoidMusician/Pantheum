"use strict";

var ls = [
	...['px','mm','cm','in','pt','pc'],
	...['em','ex','ch'],
	...['vh','vw','vmin','vmax'],
].map(c => ({
	regex:(new RegExp('^(\\d+)'+c+'$')),
	type:c
}));
function length(s) {
	if (typeof s === 'string')
		s = s.trim();
	if (!isNaN(s)) return +s;
	for (let u of ls) {
		var match = s.match(u.regex);
		if (match)
			return Object.assign(Object.create(u), {value:match[1]});
	}
	return s;
};

var ts = ['s','ms'].map(c => ({
	regex:(new RegExp('^(\\d+)'+c+'$')),
	type:c
}));
function time(s) {
	if (typeof s === 'string')
		s = s.trim();
	if (!isNaN(s)) return +s;
	for (let u of ts) {
		var match = s.match(u.regex);
		if (match)
			return Object.assign(Object.create(u), {value:match[1]});
	}
	return s;
};



var style = new Map();

function c(...n) {
	return n[0]+n.slice(1).map(a => a[0].toUpperCase()+a.substr(1)).join('');
	return n.join('-');
}

function isobj(v) {
	return (typeof v === 'object');
}
function nospread(v) {
	v[Symbol.isConcatSpreadable] = false;
	return v;
}
function isspread(v) {
	return (Array.isArray(v) && (c=>c||c===undefined)(v[Symbol.isConcatSpreadable]));
}
function type(v) {
	var typ = typeof v;
	if (typ === 'string') return typ;
	try {
		if (typ !== 'object') return typ;
		if (v instanceof String) return 'string';
		if (isspread(v))
			return 'array';
		else if (Symbol.iterator in v)
			return v[Symbol.iterator]() === v ? 'iterator' : 'iterable';
		else if (isobj(v))
			return 'object';
	} catch(e) {
		return typ;
	}
};

function _4some(v) {
	if (typeof v === 'string')
		v = v.trim().split(/\s+/g);
	if (typeof v !== 'object' || !('length' in v)) return [v,v,v,v];
	if (!v.length || v.length > 4) return undefined;
	if (v.length === 4) return v;
	return ([
		[0,0,0,0],
		[0,1,0,1],
		[0,1,2,1]
	])[v.length-1].map(i => v[i]);
}

function* collect(r, k, v) {
	if (arguments.length < 3) v = r[k];
	switch (type(v)) {
		case 'undefined':
			return; // gobble undefined
		case 'iterator':
		case 'iterable':
		case 'array':
			var iter = v[Symbol.iterator]();
			for (var n = iter.next(); !n.done; n = iter.next(r[k])) {
				yield* collect.call(this, r, k, n.value);
			}
			yield* collect.call(this, r, k, n.value);
			return;
		case 'function':
			yield* collect.call(this, r, k, v.call(this, r[k], r, k));
			return;
	}
	yield v;
}

var edges = ['top','right','bottom','left'];
var edge_regex = e => e in edge_regex ? edge_regex[e] : edge_regex[e] = new RegExp(e, 'i');
function split_edges(n, nosplit) {
	var proto = {
		toString() {
			return edges.map(e => this[e]).join(' ');
		},
	};
	proto[Symbol.iterator] = function* iterate() {
		for (let e of edges)
			yield this[e];
	};
	var v = {
		enumerable: false,
		configurable: true,
		get: function() {
			var r = Object.create(proto), vs = [];
			var last;
			for (let e of edges) {
				let v = this[c(n, e)];
				if (vs.length && v !== last)
					last = NaN;
				else last = v;
				vs.push(v);
				r[e] = v;
			}
			if (Number.isNaN(last))
				return r;
			// Single value: condense it
			return last;
		},
		set: function(r) {
			if (isobj(r)) {
				// Apply this value to all children
				for (let e of edges)
					this[c(n, e)] = r;
				// Pick out specific keys for specific children
				for (let k in r)
					for (let e of edges)
						if (edge_regex(e).test(k))
							this[c(n, e)] = r[k];
			} else {
				var _4 = nosplit ? [r,r,r,r] : _4some(r);
				for (let i=0; i<4; i++)
					this[c(n, edges[i])] = _4[i];
			}
			return this;
		},
	};
	style.set(n, v);
}
split_edges('margin');
split_edges('padding');
split_edges('border', true);

// Thanks to https://github.com/peteboere/css-crush/blob/master/misc/initial-values.ini
var defaults = {
	[c('animation','iteration','count')]: '1',
	[c('animation','play','state')]: 'running',
	[c('animation','timing','function')]: 'ease',
	[c('backface','visibility')]: 'visible',
	[c('background','attachment')]: 'scroll',
	[c('background','clip')]: 'border-box',
	[c('background','color')]: 'transparent',
	[c('background','origin')]: 'padding-box',
	[c('background','position')]: '0 0',
	[c('background','repeat')]: 'repeat',
	[c('background','size')]: 'auto auto',
	[c('border','width')]: 'medium',
	[c('border','color')]: 'inherit',
	[c('border','bottom','color')]: 'inherit',
	[c('border','bottom','width')]: 'medium',
	[c('border','collapse')]: 'separate',
	[c('border','left','color')]: 'inherit',
	[c('border','left','width')]: 'medium',
	[c('border','right','color')]: 'inherit',
	[c('border','right','width')]: 'medium',
	[c('border','top','color')]: 'inherit',
	[c('border','top','width')]: 'medium',
	[c('box','sizing')]: 'content-box',
	[c('caption','side')]: 'top',
	[c('color')]: 'inherit',
	[c('column','fill')]: 'balance',
	[c('column','rule')]: 'medium none currentColor',
	[c('column','rule','color')]: 'currentColor',
	[c('column','span')]: '1',
	[c('direction')]: 'ltr',
	[c('display')]: 'inline',
	[c('empty','cells')]: 'show',
	[c('font','family')]: 'inherit',
	[c('font','size')]: 'medium',
	[c('list','style','position')]: 'outside',
	[c('list','style','type')]: 'disc',
	[c('opacity')]: '1',
	[c('outline','color')]: 'invert',
	[c('outline','width')]: 'medium',
	[c('overflow')]: 'visible',
	[c('overflow','x')]: 'visible',
	[c('overflow','y')]: 'visible',
	[c('perspective','origin')]: '50% 50%',
	[c('position')]: 'static',
	[c('quotes')]: '"\\201C" "\\201D" "\\2018" "\\2019"',
	[c('tab','size')]: '8',
	[c('text','align')]: 'inherit',
	[c('text','decoration','color')]: 'inherit',
	[c('text','decoration','style')]: 'solid',
	[c('transform','style')]: 'flat',
	[c('transition','delay')]: '0s',
	[c('transition','duration')]: '0s',
	[c('transition','timing','function')]: 'ease',
	[c('vertical','align')]: 'baseline',
	[c('visibility')]: 'visible',
};
for (let k of [
	'animation', 'animation-fill-mode', 'animation-name',
	'background-image', 'border-style', 'border-bottom-style',
	'border-image', 'border-left-style', 'border-right-style',
	'border-top-style', 'box-shadow', 'clear', 'column-rule-style',
	'column-rule-width', 'counter-increment', 'counter-reset',
	'float', 'hyphens', 'list-style', 'list-style-image', 'max-height',
	'max-width', 'outline-style', 'perspective', 'text-decoration',
	'text-decoration-line', 'text-shadow', 'text-transform',
	'transform', 'transition', 'transition-property',
].map(a => c(...a.split('-')))) defaults[k] = 'none';
for (let k of [
	'animation-delay', 'animation-duration', 'background',
	'background-position-x', 'background-position-y',
	'border-bottom', 'border-bottom-left-radius',
	'border-bottom-right-radius', 'border-left', 'border-radius',
	'border-right', 'border-spacing', 'border-top',
	'border-top-left-radius', 'border-top-right-radius',
	'margin-bottom', 'margin-left', 'margin-right', 'margin-top',
	'min-height', 'min-width', 'orphans', 'outline',
	'padding-bottom', 'padding-left', 'padding-right', 'padding-top',
	'text-indent', 'widows',
].map(a => c(...a.split('-')))) defaults[k] = '0';
for (let k of [
	'bottom', 'clip', 'columns', 'column-count', 'column-width',
	'cursor', 'height', 'left', 'page-break-after',
	'page-break-before', 'page-break-inside', 'right',
	'table-layout', 'text-align-last', 'top', 'width', 'z-index',
].map(a => c(...a.split('-')))) defaults[k] = 'auto';
for (let k of [
	'animation-direction', 'column-gap', 'content', 'font',
	'font-style', 'font-variant', 'font-weight', 'letter-spacing',
	'line-height', 'unicode-bidi', 'white-space', 'word-spacing'
].map(a => c(...a.split('-')))) defaults[k] = 'normal';

for (let k in defaults) {
	if (style.has(k)) continue;
	let K = '_'+k;
	style.set(k, {get: function() {
		var v = this[K];
		return (typeof v === 'undefined') ? defaults[k] : v;
	}, set: function(v) {
		if (v === undefined) return;
		if (v === 'unset') {
			delete this[K];
		} else {
			if (v === null) v = defaults[k];
			if (!(K in this)) {
				// Create it as a hidden property
				Object.defineProperty(this, K, {
					enumerable: false,
					configurable: true,
					writable: true,
					value: v
				});
			} else this[K] = v;
		}
		return this;
	}});
}

var data_prefix = 'data-';
var data = {
	enumerable: false,
	configurable: true,
	get: function() {
		var r = {};
		for (let k in this) {
			if (k.startsWith(data_prefix))
				r[k.substr(data_prefix.length)] = this[k];
		}
		return r;
	},
	set: function(r) {
		if (typeof r !== 'undefined') {
			if (r === null) {
				for (let k in this)
					if (k.startsWith(data_prefix))
						delete this[k];
			} else if (typeof r === 'object') {
				for (let k in r)
					this[data_prefix+k] = r[k];
			} else if (Array.isArray(r)) {
				for (let v of r)
					data.set.call(this, v);
			}
		}
		return this;
	}
};

var simplify = {
	enumerable: false,
	configurable: true,
	value: function() {
		var dfault = {};
		var r = Object.assign({}, this, {style:{}});
		for (let [name, prop] of style) {
			var df = prop.get.call(dfault);
			if (df != undefined && df !== this.style[name])
				r.style[name] = this.style[name].toString();
		}
		return r;
	},
}

function expand(r, ...props) {
	for (let p of [r, ...props]) {
		if (r!==p)
			Object.assign(r, p, {style:r.style});
		if (typeof p.data !== 'undefined')
			data.set.call(r, p.data);
		if (p.style) {
			if (!r.style) r.style = {};
			let old = Object.assign({}, p.style);
			for (let [name, prop] of style) {
				let first = (r===p);
				for (let v of collect.call(this, old, name)) {
					if (first&&first--) delete r.style[name];
					prop.set.call(r.style, v);
					let K = '_'+name;
					if (K in r.style) {
						r.style[name] = r.style[K];
						delete r.style[K];
					}
				}
			}
		}
	}
	delete r.data;
	return r;
}
var flatten = (list) => list.reduce((V, v) => V.concat(isspread(v) ? flatten(v) : v), []);
function defprop(obj, name, prop, ...vals) {
	// Keep track of existing value, but allow ...vals to overwrite it
	if (name in obj)
		vals.unshift(obj[name]);

	// Create the property definition (getters/setters, enumerability)
	Object.defineProperty(obj, name, prop);

	// Apply all of the property values, in order
	for (let v of collect.call(this, obj, name, vals))
		obj[name] = v;
}
function live(r, ...props) {
	// Method to make this into a simple object again
	Object.defineProperty(r, 'simplify', simplify);

	// Create the data properties
	defprop.call(this, r, 'data', data);

	// Ensure we have a style
	if (!r.style) r.style = {};

	// Copy our style values to ensure they are added
	// (even if a magic method sets it in the object)
	var rstyle = Object.assign({}, r.style);

	// Initialize the style properties
	for (let [name, prop] of style)
		// r.style[name] is included by defprop automatically
		if (name in rstyle && rstyle[name] !== r.style[name])
			defprop.call(this, r.style, name, prop, rstyle[name]);
		else defprop.call(this, r.style, name, prop);

	// Merge properties from the rest of the property objects
	return merge.apply(this, arguments);
}

var _proto = live({});
function proto(r, ...props) {
	if (!_proto.isPrototypeOf(r)) {
		props.unshift(r);
		r = _proto;
	}
	r = Object.create(r);
	r.style = Object.create(r.style);
	return merge.call(this, r, ...props);
}

function merge(r, ...props) {
	for (let p of flatten(props)) {
		if (!p) continue;
		for (let k in p) {
			let v = p[k];
			if (k === 'style') {
				merge.style.call(this, r.style, v);
			} else r[k] = v;
		}
	}
	return r;
}
merge.style = function mergestyle(style, ...styles) {
	for (let S of styles) {
		for (let s in S) {
			for (let c of collect.call(this, style, s, S[s]))
				style[s] = c;
		}
	}
	return style;
}

// Utilities
expand.nospread = nospread;
expand.preserve = function(v) {
	// XXX
	if (typeof v === 'string')
		return nospread(new String(v));
	return v;
};
// Function variants
expand.merge = merge;
expand.live = live;
expand.proto = proto;
expand.make = function(...props) {
	return expand.call(this, {}, ...props);
};
expand.live.make = function(...props) {
	return expand.live.call(this, {}, ...props);
};
// Style variants
function wrapstyle(f) {
	return function style_wrapper(...props) {
		return f.apply(this, props.map(style=>({style}))).style;
	};
}
expand.style = wrapstyle(expand);
expand.style.live = wrapstyle(expand.live);
expand.style.make = wrapstyle(expand.make);
expand.style.live.make = wrapstyle(expand.live.make);
expand.style.merge = expand.merge.style;

expand.style.proto = function(r, ...props) {
	if (!_proto.style.isPrototypeOf(r)) {
		props.unshift(r);
		r = _proto.style;
	}
	r = Object.create(r);
	return merge.style.call(this, r, ...props);
};

expand.React = function(props, classes) {
	function _clsz(classes) {
		if (typeof classes === 'string')
			return classes.trim().split(/\s+/g);
		if (classes.length > 1)
			return classes.reduce((a,b)=>a.concat(_clsz(b)),[]);
		else if (classes[0] && isspread(classes[0]))
			return _clsz(classes[0]);
		return classes;
	}
	function make(cls, ...props) {
		if (type(cls) === 'object') {
			props.unshift(cls);
			cls = [];
		}
		props = _clsz(cls).map(c=>classes[c]).concat(props);
		var r = proto.call(this, make.live, ...props);
		return r.simplify();
	};
	make.live = proto({}, props);
	return make;
};

if (typeof pantheum !== 'undefined' && pantheum.view)
	pantheum.view.expand = expand;
if (typeof module !== 'undefined')
	module.exports = expand;
