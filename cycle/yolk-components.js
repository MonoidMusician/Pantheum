var getsubj = o => {
	var base = o;
	// Hack to obtain the base Subject
	while (typeof base.source === 'object') base = base.source;
	while (typeof base.value === 'object') base = base.value;
	if (typeof base._sources === 'object')
		return base._sources.map(getsubj).filter(
			o => o instanceof Rx.Subject || o instanceof Rx.BehaviorSubject
		)[0];
	return base;
}

function InputRange({props}) {
	var base = getsubj(props.value);
	return h('span', null, h('input', Object.assign({}, props, {
		type: 'range',
		onInput: e => base.onNext(Number(e.target.value)),
		value: props.value,
	})), props.value);
}

function InputText({props}) {
	var base = getsubj(props.value);
	return h('input', Object.assign({}, props, {
		onInput: e => base.onNext(e.target.value),
		value: props.value,
	}));
}

function CheckBox({props}) {
	var base = getsubj(props.value);
	return h('input', Object.assign({}, props, {
		type: 'checkbox',
		onChange: e => base.onNext(e.target.checked),
		checked: props.value,
	}))
}

var firstToUpper = s => s[0].toUpperCase() + s.slice(1);
var kebab = R.join('-');
var camel = s => s[0] + s.slice(1).map(firstToUpper).join('');

var attempt = (obj, key) =>
	obj && key in obj ? Just(obj[key]) : Nothing();
var attempt_path = (obj, path) =>
	attempt(R.path(R.init(path))(obj), R.last(path));

var Just = (...arg) => arg;
var nihil = Just();
var Nothing = () => nihil;
var Many = (...arg) => arg.reduce(R.concat);
var First = (...arg) => arg.filter(R.prop('length'))[0];

function collectprop(obj, ...path) {
	return First(
		attempt(obj, kebab(path)),
		attempt(obj, camel(path)),
		attempt_path(obj, path)
	);
}

var properties = `
	background-color
`.split('\n').map(R.trim).filter(Boolean).map(R.split('-'));

function collectprops(obj) {
	var result = [], val;
	for (let prop of properties)
		if (val = collectprop(obj, ...prop))
			result.push([prop, val]);
	return result;
}

function collectprops_camel(obj) {
	return R.map(R.adjust(camel, 0))(collectprops(obj));
}

// combine properties, styling: overwrite
// combine events: fire custom after default


function FAB____({props, children, createEventHandler: e}) {
	var waves = 'waves' in props ? props.waves : !props.className;
	return h('a', Object.assign({}, props, {
		style: maybeO(props.style||{}).map(o => Object.assign({
			textAlign: 'center',
			margin: 3,
		}, o)),
		className: maybeO(props.className).map(c => {
			if (!Array.isArray(c)) c = [c]; else c = R.flatten(c);
			c.push('btn-floating no-select');
			if (!c.includes('disabled'))
				c.push('waves-effect waves-light');
			return c;
		}),
		events: undefined,
		onMount: waves ? e => Waves.displayEffect() : undefined,
	}), children /*, props.touch && h('div', {
		style: {
			top: 0, left: 0,
			width: '100%', height: '100%',
			backgroundColor: 'white',
			opacity: 0.5,
			borderRadius: '50%',
			transform: props.touch.map(a => 'scale('+a+')'),
		},
	})/**/);
	var padding = 2;
	var size$ = props.size.map(Number);
	return h('div', Object.assign({
		style: {
			display: 'inline-block',
		},
	}, extra_events), [
		h('div', {
			style: {
				backgroundColor: props.fill,
				margin: padding,
				width: size$,
				height: size$,
				borderRadius: '50%',
				boxShadow: props.boxShadow||'',
				cursor: 'pointer',
			},
		}, h('div', {
			style: {
				position: 'absolute',
				width: size$,
				height: size$,
				borderRadius: '50%',
				fontSize: size$.map(R.multiply(0.6)),
				textAlign: 'center',
				color: props.textColor||'white',
				marginTop: size$.map(R.multiply(0.05)),
				verticalAlign: 'middle',
			},
		}, children), props.touch && h('div', {
			style: {
				width: '100%', height: '100%',
				backgroundColor: 'white',
				opacity: 0.5,
				borderRadius: '50%',
				transform: props.touch.map(a => 'scale('+a+')'),
			},
		})),
	]);
}

