var leaveprogeny = e => {
	var E = e.toElement;
	if (!E) return false;
	while (E.parentElement) {
		if (E === e.currentTarget)
			return false;
		E = E.parentElement;
	}
	return true;
};
var event_handler = {get:(target, name, proxy) => {
	if (name in target) return target[name];
	switch (name) {
		case 'hover':
			return proxy.onMouseOver.map(R.T).merge(proxy.onMouseLeave.filter(leaveprogeny).map(R.F)).distinctUntilChanged();
		case 'active':
			return proxy.onMouseDown.map(R.complement(R.prop('button'))).merge(proxy.onClick.map(R.F)).merge(global_mouse_up).combineLatest(proxy.hover, R.and).distinctUntilChanged();
		case 'merge':
			return (...arg) => {
				var ret = Object.assign({}, ...arg);
				Object.assign(ret, target.events);
				delete ret.events; delete ret.createEventHandler;
				return ret;
			};
		case 'log':
			return logger => {
				for (let k in target) {
					if (O.isObservable(target[k]))
						target[k].subscribe(v => logger(k, v));
				}
				return proxy;
			};
		case 'onEnterPress':
			return proxy.onKeyPress.filter(R.pipe(R.prop('which'), R.identical(13)));
		case 'onEscPress':
			return proxy.onKeyPress.filter(R.pipe(R.prop('which'), R.identical(27)));
	}
	return target[name] = target.createEventHandler();
}};
function createEvents(createEventHandler) {
	var events = {createEventHandler:createEventHandler};
	events.events = events;
	return new Proxy(events, event_handler);
}

var global_mouse_up = new Rx.Subject();
document.addEventListener('mouseup', function() {
	global_mouse_up.onNext(false);
});
