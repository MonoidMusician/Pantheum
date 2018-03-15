var h = Yolk.h;
var O = Rx.Observable;

// Immediately gets a value from an observable, if available
var getValue = o => {
	var v;
	o.subscribe(V => v=V).dispose();
	return v;
};

var Oo2O = o => o.publish().refCount().startWith(getValue(o));

// Ensure a (return) value is Observable
var maybeO = o => !O.isObservable(o) ? O.of(o) : o;
var maybeOFn = R.curryN(2, R.compose)(maybeO);

// Create a mapping function from an object
var obj2Fn = obj => k => obj[k];

// Math.sign, but where +/-0 becomes +/-1, respectively
var sgn = n => R.identical(n, -0) ? -1 : R.identical(n, 0) ? 1 : Math.sign(n);

var Filters = {
	'all': R.T,
	'completed': R.prop('completed'),
	'todo': R.complement(R.prop('completed')),
};

var target_value = R.path(['target', 'value']);
var toggleAll = R.ifElse(
	R.all(Filters['completed']),
	R.map(R.assoc('completed', false)),
	R.map(R.assoc('completed', true))
);
var setCompleted = R.assoc('completed');

var tf = (a,b) => c => c ? a : b;
var eq = (a,b,c) => d => d == a ? b : c;
var display$ = s => s.map(tf('', 'none'));

function TodoList({props, children, createEventHandler: e}) {
	var id = 0;
	var Item = value => ({
		completed: false,
		value: value,
		id: ++id
	});

	var add = e();
	var clearCompleted = e();
	var filtering = e();
	var toggleall = e();

	var update = e();
	var update$ = add.map(
		value => {
			if (value && value.trim().length)
				return R.append(Item(value));
			return R.identity;
		}
	).merge(update);

	var editing$ = new Rx.BehaviorSubject(null);

	var filtername$ = filtering.map(target_value).startWith('all');
	var filterfn$ = filtername$.map(R.prop(R.__, Filters));

	update$ = clearCompleted.map(R.always(R.reject(Filters['completed']))).merge(update$);
	update$ = update$.merge(toggleall.map(R.always(toggleAll)));
	update$ = update$.publish().refCount();

	var items$ = update$.startWith(R.identity).scan((acc, fn) => fn(acc), ['Hello', 'Bye'].map(Item));

	//items$.map(R.compose(R.flatten, R.map(i => [i.id, i.completed, i.value]))).subscribe(R.unary(console.log.bind(console)));/**/

	var viewing$ = filterfn$.combineLatest(items$, R.filter);
	var todo$ = items$.map(R.filter(Filters['todo']));
	var completed$ = items$.map(R.filter(Filters['completed']));

	var todo_count$ = todo$.map(R.prop('length'));
	var completed_count$ = completed$.map(R.prop('length'));

	return h('.todoapp', null, [
		h('header.header', null, [
			h('h1', null, "PRODUC 2Do"),
			h('input.new-todo', {
				placeholder: "What needs to be done?",
				onKeyPress: e => {
					if (e.which === 13) {
						var val = e.target.value;
						e.target.value = '';
						add(val);
					}
				},
				onMount: e => e.target.focus(),
			}),
		]),
		h('.main', {
			style: {
				display: display$(items$.map(R.prop('length'))),
			}
		}, [
			h('input.toggle-all', {
				type: 'checkbox',
				href: 'javascript:void(0)',
				onClick: toggleall,
				checked: items$.map(R.all(Filters['completed'])),
			}),
			h('ul.todo-list', {
				style: {
					display: display$(viewing$.map(R.prop('length'))),
				}
			}, viewing$.map(R.map(item => {
				var me = R.propSatisfies(R.identical(item.id), 'id');
				return h('li', {
					key: item.id,
					className: editing$.map(R.identical(item.id)).map(
						editing => [editing && 'editing']
					).map(item.completed ? R.append('completed') : R.identity),
					onDblClick: e => {
						e.currentTarget.getElementsByTagName('input')[1].focus();
						editing$.onNext(item.id);
					},
				}, [
					h('input.view.toggle', {
						type: 'checkbox',
						checked: O.of(!item.completed, item.completed),
						onClick: e => {
							update(R.map(R.when(
								me,
								setCompleted(e.target.checked)
							)));
						},
					}),
					h('label.view', {}, item.value),
					h('input.edit', {
						value: item.value,
						onBlur: e => {editing$.onNext(null)},
						onChange: e => {
							update(R.map(R.when(
								me,
								R.assoc('value', e.target.value)
							)));
							editing$.onNext(null);
						},
						onKeyPress: e => {
							if (e.which === 13 || e.which === 27) {
								if (e.which === 13) {
									update(R.map(R.when(
										me,
										R.assoc('value', e.target.value)
									)));
								}
								editing$.onNext(null);
								e.target.blur();
							}
						},
					}),
					h('button.destroy.view', {
						onClick: e => {
							update(R.reject(me));
						},
					}),
				]);
			}))),
		]),
		h('.footer', {
			style: {
				display: display$(items$.map(R.prop('length'))),
			}
		}, [
			h('span.todo-count', null, [
				todo_count$,
				' item',
				todo_count$.map(eq(1,'','s')),
				' left'
			]),
			h('ul.filters', null, R.values(R.mapObjIndexed(
				(text, key) => h('li', null, h('a', {
					value: key,
					onClick: filtering,
					className: filtername$.map(R.equals(key)).map(tf('selected')),
					href: 'javascript:void(0)',
				}, text)),
				{
					'all': "All",
					'todo': "Active",
					'completed': "Completed",
				}
			))),
			h('button.clear-completed', {
				onClick: clearCompleted,
				style: {
					display: display$(completed_count$),
				},
			}, "Clear completed"),
		]),
	]);
}

var status$ = new Rx.Subject();
Yolk.render(h('span', null, status$), document.getElementById('status'));
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

var app1 = h(TodoList, {title: 'Example'});
//Yolk.render(app1, document.getElementById('app1'));


var horizontal = h => '0 '+(typeof h === 'number' ? ''+h+'px' : h);

var log = a => (console.log(a), a);
var loga = (...arg) => (console.log(...arg), arg[arg.length-1]);
var log2 = R.curryN(2, R.binary(loga));
var loge = R.compose(log, R.prop('_rawEvent'));
var logc = ({_rawEvent:e}) => {
	var n = {};
	for (let k in e) {
		var type = k.match(/^(.+)([XY])$/);
		if (!type) continue;
		var i = 'XY'.indexOf(type[2]);
		type = type[1];
		n[type] = n[type] || [];
		n[type][i] = e[k];
	}
	console.log(e);
	console.log(R.flatten(R.values(n)).concat(R.keys(n)));
};

var promote = (i, j, list) => {
	if (i === j) return list;
	var sign = j > i ? +1 : -1;
	while (j > i+1 || j < i-1) {
		list = promote(i, i+sign, list);
		i += sign;
	}
	[list[j], list[i]] = [list[i], list[j]];
	return list;
};

var getheight = e => e.getClientRects()[0].height;

function DragDrop({props, children, createEventHandler: e}) {
	var fontSize$ = new Rx.BehaviorSubject(30);
	var items$ = new Rx.BehaviorSubject(['One item', 'Two time', 'Three rhyme', 'And the longest one shall wrap and create space for itself', fontSize$]);
	var status$ = new Rx.Subject();
	var baseline, index, orig;
	return h('div', null, [
		h('h1', null, h('a#dragdrop', {href:'#dragdrop'}, "Drag 'n' Drop")),
		h(InputRange, {
			min: 20, max: 100,
			value: fontSize$,
		}),
		h('ul', {
			style: {
				fontSize: fontSize$.map(a => ''+a+'pt'),
				listStyle: 'none',
			},
		}, items$.map(R.map(
			item => h('li', {
				key: item,
				style: {
					border: '1px solid gray',
					margin: 10,
					background: 'white',
				},
				onDragStart: ({_rawEvent:e}) => {
					baseline = e.pageY;
					index = (orig = items$.getValue()).indexOf(item);
				},
				onDragEnd: ({_rawEvent:e}) => {
					return;
					if (!e.screenX && !e.screenY) {console.log(e);return}
					//if (!e.pageY) {items$.onNext(orig);return}
					var delta = e.pageY-baseline;
					var items = items$.getValue().slice();
					var height = e.target.getClientRects()[0].height;
					var next = R.clamp(0, items.length-1, index+Math.round(delta/height));
					if (next === index) return;
					items = promote(index, next, items);
					baseline += (next-index)*height;
					index = next;
					items$.onNext(items);
				},
				onDrag: ({_rawEvent:e}) => {
					if (!e.screenX && !e.screenY) {return;console.log(e);return}
					//if (!e.pageY) {items$.onNext(orig);return}
					var delta = e.pageY-baseline;
					var items = items$.getValue().slice();
					/**/
					var next = index; var target = e.target; var d = delta;
					if (delta > 0 && next < items.length-1) {
						target = target.nextElementSibling;
						var h = getheight(target);
						while (next < items.length-1 && d > h) {
							next += 1;
							d -= h; baseline += h;
							target = target.nextElementSibling;
							if (!target) break;
							h = getheight(target);
						}
					} else if (delta < 0 && next > 0) {
						target = target.previousElementSibling;
						var h = getheight(target);
						while (next > 0 && d < -h) {
							next -= 1;
							d += h; baseline -= h;
							target = target.previousElementSibling;
							if (!target) break;
							h = getheight(target);
						}
					}
					/*/
					var height = e.target.getClientRects()[0].height;
					var next = R.clamp(0, items.length-1, index+Math.round(delta/height));
					/**/
					if (next === index) return;
					items = promote(index, next, items);
					index = next;
					items$.onNext(items);
				},
				draggable: true,
			}, [h('span', {
				style: {
					cursor: O.of('grab', '-webkit-grab'), // XXX: prefixing
					margin: horizontal(10),
				},
			}, '='), item])
		))),
		status$.startWith("drag to see updates"),
	]);
}

var app2 = h(DragDrop, {title: 'Example'});
//Yolk.render(app2, document.getElementById('app2'));

var SVG = type => ({props, children, createEventHandler: e}) => {
	console.log(props, children);
	var props$ = O.combineTemplate(props);
	var element;
	return h('div', {
		style: {display: 'inline-block'},
		onMount: e => {
			element = e.target.appendChild(document.createElementNS(d3.namespaces.svg, type));
			props$.subscribe(props => {
				for (let k in props) {
					console.log(k, props[k]);
					if (k === 'innerHTML')
						element.innerHTML =  props[k];
					else
						element.setAttribute(k, props[k]);
				}
			});
		},
	});
}

function request$() {}

// Send the most recent value once it remains silent for the specified interval
function throttle$(input$, time) {
	return input$.flatMapLatest(v => O.of(v).delay(time));
}


// Create a SVG path consisting of two lines, each with three points
function make_shape(contour, equal_spacing, rel) {
	rel = R.multiply(rel);
	var spacing = [4, !equal_spacing ? 14 : 20, 36];
	if (contour.length === 2)
		contour = [contour[0], contour[0], contour[0], contour[1], contour[1], contour[1]];
	else if (contour.length === 3)
		contour = contour.concat(contour);
	contour = contour.map(rel); spacing = spacing.map(rel);
	return ['M', spacing[0], contour[0], 'L', spacing[1], contour[1], 'L', spacing[2], contour[2],
			'M', spacing[0], contour[3], 'L', spacing[1], contour[4], 'L', spacing[2], contour[5]].join(' ');
}
// Cross and check shapes, respectively
var shapes = {[true]: [36, 20, 36, 4, 20, 4], [false]: [20, 32, 4]};

function Status({props, children, createEventHandler: e}) {
	var CORRECT = 1;
	var INCORRECT = -1;
	var PENDING = 0;
	var HIDDEN = null;
	var time = getValue(maybeO(props.delay||600));

	var value$ = new Rx.Subject();
	if (props.value$) value$.subscribe(getsubj(props.value$));
	var status$ = value$.map(tf(PENDING, HIDDEN)).merge(
		throttle$(value$, time).filter(Boolean)
		.flatMapLatest(maybeOFn(getValue(props.scorer)))
		.map(tf(CORRECT, INCORRECT))
	).publish().refCount().startWith(HIDDEN);

	var rel = 0.7;
	var rel$ = new Rx.BehaviorSubject(rel);
	var w$ = rel$.map(rel => rel < 0.5 ? 2 : 4);
	var m$ = w$.map(w => [20, 20]);
	var loading$ = rel$.combineLatest(m$, (rel, contour) => [contour, false, rel]);
	var Loading$ = loading$.map(([a,b,c]) => [a, !b, c]);
	loading$[true] = loading$;
	loading$[false] = Loading$;

	var d = 220;
	var dash = '1,7';
	var dash_length = R.sum(dash.split(',').map(Number));
	var nodash = dash_length+',0';

	var shape$ = status$.map(Number).scan((acc, v) => [acc[1], v, v || acc[1]], [0, 0, 0]).flatMapLatest(
		// Ensure we transition between the appropriate shape and loading bar
		([S, s, $]) =>
		!$ ? loading$[R.identical(0,s)] :
		(a => b => s ? a.startWith(getValue(b)) : a.take(1).concat(b))
		(rel$.map(r => [shapes[$<0], $<0, r])) // <= a
		(loading$[$>0]) // <= b
	).distinctUntilChanged().flatMapLatest(animate(
		R.apply(make_shape),
		// Remove transition between loading and Loading
		(_,__,[n],[v]) => n.length === 2 && R.equals(n, v) ? 0 : d,
		// Aim for a 3D rotating sort of look
		d3.easeSinOut
	));
	var color$ = status$.map(Number).flatMapLatest(animate({
		[CORRECT]:   '#00e676',
		[INCORRECT]: '#d50000',
		[PENDING]:   '#dfdfdf', // Pending/hidden
		/*
		[CORRECT]:   '#459845',
		[INCORRECT]: '#DC4B1C',
		[PENDING]:   '#909090', // Pending/hidden
		/**/
	}, d, d3.easeCubic, d3.interpolateColor));
	var dasharray$ = status$.map(eq(PENDING, dash, nodash));
	var offset$ = wrapping(d, -dash_length);

	return h('div', null, [
		h('input', {
			onInput: e => {value$.onNext(e.target.value)},
			placeholder: 'Type "hello"',
			className: status$.map(s => {
				switch (s) {
					case CORRECT:
						return 'valid';
					case INCORRECT:
						return 'invalid';
					default:
						return 'validate';
				}
			}),
			style: {
				width: rel$.map(rel => 'calc(100% - '+(40*rel)+'px)'),
			},
		}),
		h('div', {
			title: status$.map(obj2Fn({
				[CORRECT]:   'Correct',
				[INCORRECT]: 'Incorrect',
				[PENDING]:   'Waiting...',
			})),
			style: {
				display: 'inline-block',
				verticalAlign: rel$.map(R.multiply(-15)),
				visibility: status$.map(eq(HIDDEN, 'hidden', '')),
				//width: '60px',
			},
			onMount: e => {
				var svg = d3.select(e.target).insert('svg');
				svg.style('display', 'inline-block')
				svg.style('margin', 'auto');
				var symbol = svg.append('path').style('fill', 'none').style('stroke-linecap', 'round');

				rel$.subscribe(rel => svg.attr('width', 40*rel).attr('height', 40*rel));
				w$.subscribe(w => symbol.style('stroke-width', w+'px'));
				color$.subscribe(stroke => symbol.style('stroke', stroke));
				dasharray$.subscribe(dashes => symbol.attr('stroke-dasharray', dashes));
				shape$.subscribe(shape => symbol.attr('d', shape));
				offset$.subscribe(offset => symbol.attr('stroke-dashoffset', offset));
			}
		}),
		//h(InputRange, {min: 0.5, max: 1.0, step: 0.1, value: rel$}),
	]);
}


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
function wrapping(speed, multiply=1) {
	var first;
	if (!frameHandle) tick();
	return time$.map(time => {
		if (!first) first = time;
		return ((time-first)/speed % 1)*multiply;
	}).startWith(0);
}
function cycleBetween(speed, min=0, max=1, easing, easing2) {
	var output$ = wrapping(speed, 2);
	if (easing && easing2) output$ = output$.map(v => v < 1 ? easing2(1-v) : easing(v-1));
	if (!easing || !easing2) output$ = output$.map(v => v < 1 ? 1-v : v-1);
	if (easing && !easing2) output$ = output$.map(easing);
	return output$.map(v => (max-min)*v+min);
}
function animate(values, timing, easing, interpolate) {
	if (!easing) easing = R.identity;
	if (typeof timing === 'number') timing = R.always(timing);
	var id = 0, current, current_value;
	return function(value) {
		var goal = typeof values === 'function' ? values(value) : values[value];
		if (current === undefined) {
			current = goal;
			current_value = value;
		}
		var timed;
		if (goal !== current && (timed = timing(goal, current, value, current_value))) {
			var first, inter = interpolate;
			if (!inter) {
				if (typeof d3 == 'object')
					inter = d3.interpolate(current, goal);
				else inter = ((a, b) => (rel) => rel*b+a)(current, goal-current);
			}
			var _id = ++id;
			if (!frameHandle) tick();
			return time$.map(time => {
				var rel;
				if (first === null) return;
				else if (first === undefined) first = time;
				if (time >= first+timed) {
					first = null;
					rel = 1;
					current = goal;
					current_value = value;
				} else {
					rel = ((time-first)/timed);
					if (easing) rel = easing(rel, goal, current, value, current_value);
				}
				current = inter(rel);
				current_value = value;
				if (_id!==id) return;
				else return current;
			}).takeWhile(x=>x!=null);
		} else {
			current_value = value;
			current = goal;
			return Rx.Observable.just(goal);
		}
	}
}

var maxOf = R.reduce(R.max, -Infinity);
var minOf = R.reduce(R.min, Infinity);

var scale_time;
scale_time = (a,b) => a/b;
scale_time = (a,b) => Math.pow(Math.abs(a/b), 1/4);
function actuate(values, maxtime, ...rest) {
	var maxdist = maxOf(values)-minOf(values);
	var speed = maxtime/maxdist;
	var timing = (a,b) => Math.abs(maxtime*scale_time((b-a), maxdist));
	return animate(values, timing, ...rest);
}

var asym_ease = pow => (x,y)=>Math.pow(x, y > 0 ? pow : 1/pow);
var velocity = speed => (goal, current) => speed*Math.abs(goal-current);


var getanswer = name => {
	var subj = new Rx.Subject();
	var req = fetch('answers/'+name);
	req = req.then(req => {
		if (!req.ok) throw new Error(req.statusText);
		return req.body.getReader().read();
	}).then(({value})=>[...value]);
	req = req.then(R.map(String.fromCodePoint)).then(R.join(''));
	req.then(result => {
		subj.onNext(result === 'true');
		subj.onCompleted();
	}, e => {
		if (e) {
			subj.onNext(false);
			subj.onCompleted();
		} else throw e;
	});
	return subj;
};
var memoized = (fn => {
	var cache = {};
	return name => {
		if (name in cache) return cache[name];
		var r = fn(name), value;
		r.subscribe({
			onNext: (v => value=v),
			onError: (e => cache[name]=O.throw(e)),
			onCompleted: (() => cache[name]=O.of(value)),
		});
		return r;
	}
})(getanswer);

var VALUE$ = new Rx.Subject();
var app3 = h(Status, {
	//scorer: R.equals('hello'), // TODO: network request
	scorer: memoized,
	delay: 1000,
	value$: VALUE$,
});
Yolk.render(app3, document.getElementById('app3'));

var applyobj = (obj, method, data) => {
	for (let k in data) {
		obj[method](kebabcase(k), data[k]);
	}
	return obj;
}
var kebabcase = camelKey => camelKey.replace(/([a-z])([A-Z])/g, (_, _1, _2) => _1+'-'+_2.toLowerCase());
var boundsetter = R.curry((obj, method, key) => value => obj[method](key, value));
function D3bind(obj, props) {
	props = Object.assign({style:{}}, props);
	var set = boundsetter(obj);
	var style = props.style; delete props.style;
	for (let [setter, data] of [
		[set('style'), style],
		[set('attr'), props]
	]) {
		for (let key in data) {
			var value = data[key];
			var method = setter(kebabcase(key));
			if (O.isObservable(value))
				value.subscribe(method);
			else method(value);
		}
	}
}

var rotatedeg = n => 'rotate('+n+'deg)';

function FAB({props, children, createEventHandler: e}) {
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
	var size$ = props.size.map(Number);
	return h('div', Object.assign({
		style: {
			display: 'inline-block',
		},
	}, extra_events), [
		h('div', {
			style: {
				backgroundColor: props.fill,
				margin: padding$,
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

function QuizQuestion({props, children, createEventHandler: e}) {
	var clearpreviousstate;
	function setstatus(status, content, clearstate) {
		if (clearpreviousstate)
			clearpreviousstate();
		clearpreviousstate = clearstate;
		status$.onNext(status);
		content$.onNext(content);
	}
	// State Observables
	var status$ = new Rx.BehaviorSubject('initial');
	var values$ = new Rx.BehaviorSubject();
	var content$ = new Rx.BehaviorSubject();
	var score$ = new Rx.BehaviorSubject();
	var progress$ = values$.map(values => values && values.filter(Boolean).length/values.length);

	// Display Observables
	var classes$ = status$.map(obj2Fn({
		'loading': 'disabled',
		'completed': 'disabled',
	}));
	var color$ = status$.combineLatest(classes$).flatMapLatest(([s,c]) => {
		if (c === 'disabled') return O.of(null);
		if (s === 'scored') return score$.map(score);
		return O.of('#80cbc4'); // default color
	});
	var checked$ = status$.map(s => s !== 'completed' && s !== 'initial');

	// Actions
	var load = values$ => {
		var value$ = new Rx.Subject();
		var node = h(Status, {
			scorer: R.equals('hello'),
			delay: 1000,
			value$,
		});
		value$.combineLatest().subscribe(values$);
		return O.of(node).delay(2000).toPromise();
	};
	var getScore = score$ => {
		return O.of(Math.random()).delay(1000).toPromise().then(score => {
			score$.onNext(score);
			return ['hello ', score];
		});
	};
	var actions = {
		load: setstate => {
			var value$ = new Rx.Subject();
			var node = h(Status, {
				scorer: R.equals('hello'),
				delay: 1000,
				value$,
			});
			return O.of(value$.combineLatest()).delay(2000).toPromise().then((values$) => {
				setstate('progress', node);
				return values$;
			});
		},
		score: setstate => {
			return O.of(Math.random()).delay(1000).toPromise().then((score) => {
				setstate('scored', ['hello ', score]);
				return score;
			});
		},
	}

	return h('div', null, h(Spinner, {
		active: status$.map(obj2Fn({
			'loading': null,
			'grading': null,
			'progress': Oo2O(progress$.flatMapLatest(animate([0,1], 500, d3.easeBounce))),
		})),
	}, checked$.map(c => h(c ? FAB : 'span', {
		style: {
			backgroundColor: color$,
			borderRadius: checked$.map(tf('50%','20%')),
			fontWeight: checked$.map(tf('bold')),
			//transitionProperty: 'box-shadow, color, border-radius', // exclude background-color
		},
		className: classes$,
		onClick: e => {
			var s = status$.getValue();
			if (s === 'initial') {
				setstatus('loading');
				//load(values$).then(setstatus.bind(null, 'progress'));
				actions.load(setstatus).then(o => o.subscribe(values$));
			} else if (s === 'progress') {
				setstatus('grading');
				//getScore(score$).then(setstatus.bind(null, 'scored'));
				actions.score(setstatus).then(score$.onNext.bind(score$));
			} else if (s === 'scored') {
				setstatus('completed');
			} else if (s === 'completed') setstatus('initial');
		},
	}, children))), content$);
}

var gray = c => Object.assign(d3.hsl(c), {s:0});
var red = d3.color('#DC4B1C');
var green = d3.color('#00C853'); // 008C00 // 459845
var dred = d3.color('#B71C1C');
var darken = (color, amnt=1) => d3.color(color).darker(amnt).toString();
var interpolate_many = (interpolate, ...data) => {
	var segments = R.map(R.apply(interpolate))(R.aperture(2)(R.flatten(data)));
	var l = segments.length;
	var floor_clamp = R.compose(R.clamp(0, l-1), Math.floor);
	return n => n!==n?data[data.length-1]:(i => segments[i](n*l-i))(floor_clamp(n*l));
}
var score = interpolate_many(d3.interpolateHcl, dred, green);
score = R.compose(/*gray, /**/score, d3.easeQuadIn);


var nesteddiv = (o, props={}) => {
	if (!o) return;
	if (typeof o === 'string') return h('div', Object.assign(props[o]||{}, {className: o}));
	if (Array.isArray(o)) return o.map(a => nesteddiv(a, props));
	var res = [];
	for (let className in o)
		res.push(h('div', Object.assign(props[className]||{}, {className}), nesteddiv(o[className], props)));
	return res;
};

var spincontents = {
	"circle-clipper left": "circle",
	"gap-patch": "circle",
	"circle-clipper right": "circle",
};
var spinners = {
	"spinner-layer spinner-blue": spincontents,
	"spinner-layer spinner-red": spincontents,
	"spinner-layer spinner-yellow": spincontents,
	"spinner-layer spinner-green": spincontents,
};
var spinner = {"preloader-wrapper active":spinners};
var create_spinner = (props={}, ...extra) =>
nesteddiv(spinner, Object.assign({[Object.keys(spinner)[0]]:props}, ...extra));

function Spinner({props, children}) {
	if (children && !('%nochildren%' in props)) {
		return h('div', {
			style: {
				position: 'relative',
				display: 'inline-block',
			},
		}, h(Spinner, Object.assign({}, props, {'%nochildren%':true})), children);
	}
	var size$ = props.size||43;
	var active$ = props.active;
	var styledisplayloading = () => ({
		style: {
			display: active$.map(active => active === null ? '' : 'none'),
		},
	});
	return h('div', {
		style: {
			position: 'absolute',
		}
	}, h('.preloader-wrapper.active', {
		style: {
			width: size$, height: size$,
			display: active$.map(active => active === undefined ? 'none' : ''),
			animation: active$.map(active => active === null ? '' : 'none'),
		}
	}, h('.spinner-layer', {
		id: 'hello',
		style: {
			display: active$.map(active => active === null ? 'none' : ''),
			animation: 'none',
			borderColor: active$.filter(a => a != null).map(d3.interpolateHsl('#e8eaf6', '#1a237e')),
		}
	}, [
		h('.circle-clipper.left', null, h('.circle', {
			style: {
				animation: 'none',
				transform: active$.filter(a => a != null).map(n => 180*(1-n)-45).map(rotatedeg),
			}
		})),
		h('.gap-patch', null, h('.circle')),
		h('.circle-clipper.right', null, h('.circle', {
			style: {
				animation: 'none',
				transform: active$.filter(a => a != null).map(n => 45-180*(1-n)).map(rotatedeg),
			}
		})),
	]), nesteddiv(spinners, R.map(styledisplayloading, spinners)))
	);
}

var gray = l => Object.assign(d3.hsl(), {l}).toString();


var grade$ = new Rx.BehaviorSubject(0);
var i$ = new Rx.BehaviorSubject(10);
var color$ = cycleBetween(4000)/*.map(d3.easePoly)/**/.map(score);

var touch$ = new Rx.BehaviorSubject(false);
var checked$ = new Rx.BehaviorSubject(false);
var rotate_ = new Rx.BehaviorSubject(0);
var rotate_ = VALUE$.map(v => +!!v).startWith(0).distinctUntilChanged();
var rotate$ = Oo2O(rotate_.map(n => n < 0.03 ? 0.03 : n).flatMapLatest(animate(R.identity, 500, d3.easeBounce)));

var app4 = h('div', {
	style: {
		//fontFamily: JSON.stringify('Linux Biolinum'),
	},
}, [
	h(QuizQuestion, {
		
	}, i$),
	h(FAB, {
		style: {
			backgroundColor: grade$.map(score),
			borderRadius: checked$.map(tf('50%','33%')),
			fontWeight: checked$.map(tf('bold')),
			transitionProperty: 'box-shadow, color, border-radius', // exclude background-color
		},
		//className: checked$.map(tf('red accent-3','red darken-4')),
		onClick: e => checked$.onNext(!checked$.getValue()),
	}, i$),
	h(FAB, {
		style: {
			backgroundColor: color$, // exclude background-color
			transitionProperty: 'box-shadow, color',
			fontWeight: checked$.map(tf(undefined, 'bold')),
		},
	}, i$.map(R.add(1))),
	h('div', {
		style: {
			position: 'relative',
			display: 'inline-block',
		},
	}, [
		h('div', {
			style: {
				position: 'absolute',
			}
		}, h('.preloader-wrapper.active', {
			style: {
				width: 43, height: 43,
				animation: 'none',
			}
		}, h('.spinner-layer', {
			style: {
				animation: 'none',
				borderColor: rotate$.map(d3.interpolateHsl('#e8eaf6', '#1a237e')),
			}
		}, [
			h('.circle-clipper.left', null, h('.circle', {
				style: {
					animation: 'none',
					transform: rotate$.map(n => 180*(1-n)-45).map(rotatedeg),
				}
			})),
			h('.gap-patch', null, h('.circle')),
			h('.circle-clipper.right', null, h('.circle', {
				style: {
					animation: 'none',
					transform: rotate$.map(n => 45-180*(1-n)).map(rotatedeg),
				}
			})),
		]))),/**/
		h(FAB, {
			className: rotate_.map(R.equals(1)).map(tf('blue darken-4','disabled')),
		}, i$.map(R.add(2))),
	]),
	h('div', {
		style: {
			position: 'relative',
			display: 'inline-block',
		},
	}, [
		h('div', {
			style: {
				position: 'absolute',
			}
		}, create_spinner({
			style: {
				width: 43, height: 43,
			}
		})),/**/
		h(FAB, {
			style: {
				color: 'black',
				boxShadow: 'none',
				position: 'relative',
			},
			className: touch$.map(tf('red lighten-2','grey lighten-2')),
			onMouseOver: e => touch$.onNext(true),
			onMouseLeave: e => touch$.onNext(false),
		}, [
			h('span', {
				style:{
					opacity: touch$.map(Number),
					transition: 'inherit',
					transitionProperty: 'opacity',
					position: 'absolute',
					textAlign: 'center',
					width: '100%',
					left: 0,
				}
			}, 'x'),
			h('span', {
				style:{
					textAlign: 'center',
					opacity:touch$.map(R.not).map(Number),
					transition: 'inherit',
					transitionProperty: 'opacity',
					color: '#9F9F9F',
				}
			}, i$.map(R.add(3))),
		]),
	]),
	h('br'),
	//h(InputRange, {value: rotate_, min: 0, max: 1, step: 1/4}),
	h(InputRange, {value: grade$, min: 0, max: 1, step: 0.1}),
	h(InputText, {type:'number',value: i$, min: 0, max: 60}),
]);
Yolk.render(app4, document.getElementById('app4'));

//Yolk.render(, document.getElementById('app5'));


