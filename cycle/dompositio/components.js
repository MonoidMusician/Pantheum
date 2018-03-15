var combine = {
	event: function(...callbacks) {
		callbacks = R.flatten(callbacks);
		if (callbacks.all(c => typeof c === 'function')) {
			if (callbacks.length < 2) return callbacks[0];
			return (...arg) => {
				// TODO: return value?
				for (let c of callbacks)
					c(...arg);
			};
		}
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



