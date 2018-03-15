var O = Rx.Observable;

var lastarg = R.unapply(R.last);
var objtofn = o => p => o[p];
var issimple = o => ['null','undefined','number','string','boolean','function'].includes(typeof o) || o instanceof String;

/*
var memoize1 = fn => {
	var cache = [];
	return function recurse(arg) {
		for (let c of cache)
			if (R.equals(c[0], arg)) return c[1];
		var result = fn(arg, recurse);
		cache.push([arg, result]);
		return result;
	};
};
var memoize2 = fn => {
	var cache = [];
	return function recurse(...arg) {
		for (let c of cache)
			if (R.equals(c[0], arg)) return c[1];
		var result = fn(...arg, recurse);
		cache.push([arg, result]);
		return result;
	};
};
*/

var push = (vs,v) => (vs.push(v), v);
var push2 = (vs,a,b) => (vs.push([a,b]), b);
var false$ = O.of(false), true$ = O.of(true);

var ensureObservable = (o/*, stack=[[false,false$],[true,true$]]*/) => {
	for (let [k,v] in stack)
		if (k === o) return v;
	if (issimple(o)) return push2(stack, o, O.of(o));
	if (O.isObservable(o)) return o;
	if (Array.isArray(o))
		return o.all(issimple) ? O.of(o) : O.combineLatest(...o.map(ensureObservable));
	var simple = true, ks = [], os = [];
	for (let k in o) {
		let v = o[k];
		simple = simple && issimple(v);
		ks.push(k);
		os.push(ensureObservable(v));
	}
	return simple ? O.of(o) : O.combineLatest(...os, (...vs) => {
		var r = {};
		for (let k of ks) r[k] = vs.shift();
		return r;
	});
};
var ensureOfObservables = o => {
	if (typeof o !== 'object') return o;
	if (Array.isArray(o)) return o.map(ensureObservable);
	var r = {};
	for (let k in o) {
		r[k] = ensureObservable(o[k]);
	}
	return r;
};

var combine = {
	event: function(...callbacks) {
		callbacks = R.flatten(callbacks);
		if (callbacks.all(c => typeof c === 'function'))
			return (...arg) => {
				// TODO: return value?
				for (let c of callbacks)
					c(...arg);
			};
		var subj = new Rx.Subject();
		callbacks.forEach(subj.subscribe.bind(subj));
		// TODO: multiple arguments?
		return subj.onNext.bind(subj);
	},
	style$: function(...values) {
		return values[values.length-1];
		return O.merge(...values.map(ensureObservable));
	},
	attributes: lastarg,
	parameters: lastarg,
	classes$: function(classes) {
		return O.combineLatest(...classes.map(ensureObservable)).map(combine.classes);
	},
	classes: function(classes) {
		if (classes == null || typeof classes === 'boolean')
			return '';
		if (typeof classes === 'string' || classes instanceof String)
			return String(classes);
		if (Array.isArray(classes))
			return classes.map(combine.classes).join(' ');
		if (typeof classes === 'object')
			return combine.classes(R.filter(objtofn(classes), R.keys(classes)));
		return classes;
	},
};
/*
var recursive = Object<Observable||recursive>;
options = {
	context: Object<any>,
	parameters: Object<any>,
	attributes: recursive,
	style: recursive,
	children: Observable<Node?>,
}
*/
function h(tag, options, ...children) {
	var events = [], attributes, styles, parameters;
	parameters = Object.assign({}, options.parameters || options);
	for (let k of Object.keys(parameters)) {
		if (k.endsWith('$')) {
			parameters[k] = ensureObservable(parameters[k]);
		} else if (!((k+'$') in parameters)) {
			parameters[k+'$'] = O.of(parameters[k]);
		}
	}
	if (typeof options.events === 'object') {
		for (let event in options.events) {
			var handler = options.events[event];
			events.push(new Event(event, handler));
		}
	}
	styles = ensureOfObservables(options.styles||{});
	attributes = ensureOfObservable(options.attributes||{});
	children = ensureObservable(children);
	tag = typeof tag === 'function' ? tag : new Tag(tag);
	if (tag.id && !('id' in attributes))
		attributes.id = tag.id;
	if (tag.classes)
		attributes.classes = [attributes.classes, tag.classes];
	return {tag, context, parameters, attributes, events, styles, children};
}

var ComponentAdapter = {
	create(properties) {
		return properties.tag(properties);
	},
};
var YolkAdapter = {
	reduce: 'apply',
	create({tag, attributes, events, styles, children}) {
		var properties = {style:{}};
		Object.assign(properties, attributes);
		for (let [event, handler] in events)
			properties[event.on().camel()] = handler;
		for (let [property, value$] in styles)
			properties.style[property.camel()] = value$;
		if (attributes.classes) {
			properties.className = combine.classes$(properties.classes);
			delete properties.classes;
		}
		var children$ = children.map(R.map(YolkAdapter.create));
		return Yolk.h(tag, properties, children);
	},
};
var D3Adapter = {
	create() {
		// ...events, style$, attribute$
		
	},
	event(event, handler) {
		var name = event.lower();
		if (name.startWith('on')) name = name.substr(2);
		this.on(name, handler);
		return new Rx.Disposable(() => this.on(name, null));
	},
	style$(property, value$) {
		return value$.subscribe(D3Adapter.style.bind(this, property));
	},
	style(property, value) {
		var name = property.kebab();
		this.style(name, value);
		return new Rx.Disposable(() => this.style(name, null));
	},
	attribute$(property, value$) {
		return value$.subscribe(D3Adapter.attribute.bind(this, property));
	},
	attribute(property, value) {
		var name = property.kebab();
		this.attr(name, value);
		return new Rx.Disposable(() => this.attr(name, null));
	},
};

var tagexp = /^(?:([_a-zA-Z0-9-]+):)?([_a-zA-Z0-9-]+)((?:[.][_a-zA-Z0-9-]+)*)(?:[#]([_a-zA-Z0-9-]+))?((?:[.][_a-zA-Z0-9-]+)*)$/;

function Tag(name) {
	var matches = tagexp.exec(name);
	this.ns = matches[1];
	this.namespace = matches[1] && d3.namespaces[matches[1]];
	this.base = new Name(matches[2]);
	this.classes = ((matches[3]||'')+(matches[5]||'')).split('.').filter(Boolean);
	this.id = matches[4];
}
Tag.prototype.toString = function() {
	var t = this.base;
	if (this.ns) t = this.ns+':'+t;
	if (this.id) t += '#'+this.id;
	t += this.classes.map(c => '.'+c).join('');
	return t;
};
function Name(...parts) {
	this.parts = [];
	for (let part of parts) {
		this.parts.push(...part.split(/-|(?=[A-Z][a-z])/g).map(s => s.toLowerCase()));
	}
};
Name.prototype.lower = function() {
	return this.parts.join('');
};
Name.prototype.kebab = function() {
	return this.parts.join('-');
};
Name.prototype.camel = function() {
	return this.parts[0] + this.parts.slice(1).map(s => s[0].toUpperCase() + s.slice(1)).join('');
};
Name.prototype.on = function() {
	return new Name('on', ...this.parts);
};



var time$ = new Rx.Subject();

var frameHandle;
function tick(timestamp) {
	if (time$.isDisposed) {
		window.cancelAnimationFrame(frameHandle);
		return;
	}

	time$.onNext(timestamp);

	frameHandle = requestAnimationFrame(tick);
}
function wrapping$(speed, multiply=1) {
	var first;
	if (!frameHandle) tick();
	return time$.map(time => {
		if (!first) first = time;
		return ((time-first)/speed % 1)*multiply;
	}).startWith(0);
}
function cycleBetween$(speed, min=0, max=1, easing, easing2) {
	var output$ = wrapping(speed, 2);
	if (easing && easing2) output$ = output$.map(v => v < 1 ? easing2(1-v) : easing(v-1));
	if (!easing || !easing2) output$ = output$.map(v => v < 1 ? 1-v : v-1);
	if (easing && !easing2) output$ = output$.map(easing);
	return output$.map(v => (max-min)*v+min);
}



