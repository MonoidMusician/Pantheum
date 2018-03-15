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
