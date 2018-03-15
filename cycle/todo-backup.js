var h = Yolk.h;
var O = Rx.Observable;

// Immediately gets a value from an observable, if available
var getValue = o => {
	var v;
	o.subscribe(V => v=V).dispose();
	return v;
};

// Ensure a (return) value is Observable
var maybeO = o => typeof o !== 'object' ? O.of(o) : o;
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

	return h('div.todoapp', null, [
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
		h('div.main', {
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
		h('div.footer', {
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
		h('h1', null, "Drag 'n' Drop"),
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
Yolk.render(app2, document.getElementById('app2'));

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

	var value$ = new Rx.Subject();
	var status$ = value$.map(tf(PENDING, HIDDEN)).merge(
		throttle$(value$, 600).filter(Boolean)
		.flatMap(maybeOFn(getValue(props.scorer)))
		.map(tf(CORRECT, INCORRECT))
	).startWith(HIDDEN);

	var rel = 0.7;
	var rel$ = new Rx.BehaviorSubject(rel);
	var w$ = rel$.map(rel => rel < 0.5 ? 2 : 4);
	var m$ = w$.map(w => [20+w/2, 20-w/2]);
	var loading$ = rel$.combineLatest(m$, (rel, contour) => [contour, false, rel]);
	var Loading$ = loading$.map(([a,b,c]) => [a, !b, c]);
	loading$[true] = loading$;
	loading$[false] = Loading$;

	var d = 220;
	var dash = '4,4';
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
		d3.sinOut
	));
	var color$ = status$.map(Number).flatMapLatest(animate({
		[CORRECT]:   '#459845',
		[INCORRECT]: '#DC4B1C',
		[PENDING]:   '#909090', // Pending/hidden
	}, d/2, d3.easeCubic));
	var dasharray$ = status$.map(eq(PENDING, dash, nodash));
	var offset$ = wrapping(d, -dash_length);

	return h('div', null, [
		h('input', {onInput: e => {value$.onNext(e.target.value)}}),
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
				width: '60px',
			},
			onMount: e => {
				var svg = d3.select(e.target).insert('svg');
				svg.style('display', 'inline-block')
				svg.style('margin', 'auto');
				var symbol = svg.append('path').style('fill', 'none');

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


var app3 = h(Status, {
	scorer: R.equals('hello') // TODO: network request
});
//Yolk.render(app3, document.getElementById('app3'));

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

var rotatedeg = n => 'rotate(-'+n+'deg)';

function Circle({props, children, createEventHandler: e}) {
	var padding = 2;
	var size$ = props.size;
	var strokeWidth$ = props.strokeWidth;
	var ndashes$ = props.ndashes;
	var center$ = size$.map(v => [v/2, v/2]);
	var radius$ = size$.combineLatest(strokeWidth$, (s, w) => s/2-w/2-padding);
	var extra_events = props.events ? getValue(props.events)(e) : {};
	var extramount;
	if (extra_events && 'onMount' in extra_events) {
		extramount = extra_events.onMount;
		delete extra_events.onMount;
	}
	return h('div', Object.assign({
		style: {
			display: 'inline-block',
		},
		onMount: e => {
			var svg = d3.select(e.target).insert('svg');
			svg.style('display', 'inline-block')
			svg.style('margin', 'auto');
			D3bind(svg, {
				width: size$, height: size$,
			});
			D3bind(svg.append('circle'), {
				style: {
					fill: props.fill,
					stroke: props.fill,
					strokeWidth: strokeWidth$.map(R.add(-0)),
					transform: ndashes$.map(n => (90/n)+90).combineLatest(props.rotation, R.add).map(rotatedeg),
					transformOrigin: center$.map(([x,y]) => [x, 'px', ' ', y, 'px'].join('')),
					opacity: props.opacity,
				},
				cx: center$.map(R.head),
				cy: center$.map(R.tail),
				r: radius$,
			});
			D3bind(svg.append('circle'), {
				style: {
					fill: 'none',
					stroke: props.stroke,
					strokeWidth: strokeWidth$,
					strokeDasharray: props.strokeLineCap.flatMapLatest(
						lc => lc === 'round'
						? radius$.combineLatest(ndashes$, strokeWidth$, (r,n,s) => (e=>(e-s)+','+(e+s))(r*Math.PI/n))
						: radius$.combineLatest(ndashes$, (r,n,s) => (e=>e+','+e)(r*Math.PI/n))
					),
					strokeLinecap: props.strokeLineCap,
					transform: ndashes$.map(n => (90/n)+90).combineLatest(props.rotation, R.add).map(rotatedeg),
					transformOrigin: center$.map(([x,y]) => [x, 'px', ' ', y, 'px'].join('')),
					opacity: props.opacity,
				},
				cx: center$.map(R.head),
				cy: center$.map(R.tail),
				r: radius$,
			});
			if (typeof extramount === 'function') extramount(e);
		}
	}, extra_events), [
		h('div', {
			style: {
				position: 'absolute',
				width: size$.map(R.multiply(1)), height: size$.map(R.multiply(1)),
				textAlign: 'center',
				fontSize: size$.map(R.multiply(0.6)),
			}
		}, h('div', {
			style:{
				position:'relative',
				top: size$.map(R.multiply(0.15)),
				//left: size$.map(R.multiply(-0.02)),
				color: props.textColor||'white',
			}
		}, children)),
		h('div', {
			style: {
				position: 'absolute',
				margin: padding,
				width: size$.map(R.add(-padding*2)),
				height: size$.map(R.add(-padding*2)),
				borderRadius: size$.map(R.multiply(0.5)),
				boxShadow: props.boxShadow,
			}
		}, h('div', {
			style: {
				zIndex: 10,
				width: '100%', height: '100%',
				backgroundColor: 'white',
				opacity: 0.5,
				borderRadius: size$.map(R.add(-padding*2)).map(R.multiply(0.5)),
				transform: props.touch.map(a => 'scale('+a+')'),
			}
		})),
	]);
}

var gray = c => Object.assign(d3.hsl(c), {s:0});
var red = d3.color('#DC4B1C');
var green = d3.color('#008c00'); // 459845
var dred = red.darker(1.5);
var darken = (color, amnt=1) => loga(d3.color(color), d3.color(color).darker(amnt));
var interpolate_many = (interpolate, ...data) => {
	var segments = R.map(R.apply(interpolate))(R.aperture(2)(R.flatten(data)));
	var l = segments.length;
	var floor_clamp = R.compose(R.clamp(0, l-1), Math.floor);
	return n => (i => segments[i](n*l-i))(floor_clamp(n*l));
}
var score = interpolate_many(d3.interpolateHcl, dred, green);
score = R.compose(/*gray, /**/score, d3.easeQuadIn);

var ndashes$ = new Rx.BehaviorSubject(0);
var strokeLineCap$ = new Rx.BehaviorSubject(false);
var size$ = new Rx.BehaviorSubject(40);
var i$ = new Rx.BehaviorSubject(10);
var strokeWidth$ = cycleBetween(2000, 2, 4, d3.easeBounce, d3.easeSinOut);
var color$ = cycleBetween(4000)/*.map(d3.easePoly)/**/.map(score);
var smallrotate = wrapping(2000).map(d3.easeSin).map(R.multiply(90));
var fastrotate = wrapping(6000).map(d3.easeExpInOut).map(R.multiply(360*2));
strokeWidth$ = new Rx.BehaviorSubject(1);
var touch$ = new Rx.BehaviorSubject(0);
var checked$ = new Rx.BehaviorSubject(false);
var app4 = h('div', {
	style: {
		fontFamily: JSON.stringify('Linux Biolinum'),
	},
}, [
	h(Circle, {
		//opacity: cycleBetween(5000, 0.5, 1, d3.easePolyInOut),
		strokeWidth: strokeWidth$,
		size: size$,
		ndashes: 0,
		rotation: 0,//fastrotate,
		fill: ndashes$.map(score),
		stroke: 'none',
		strokeLineCap: strokeLineCap$.map(tf('round')),
		touch: touch$.flatMapLatest(actuate([0,1], 100, d3.easePolyOut)),
		boxShadow: checked$.map(Number).startWith(0).flatMapLatest(
			animate([
				'0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24)',
				'0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23)',
			], 200)
		),
		events: e => {
			var events = createEvents(e);
			events.active.map(Number).subscribe(touch$.onNext.bind(touch$));
			events.onClick.subscribe(e => {
				checked$.onNext(!checked$.getValue());
			});
			events.onSelectStart = e => {e.preventDefault()};
			events.onMount = e => {e.target.onselectstart = events.onSelectStart};
			return events.merge();
		},
	}, i$),
	h(Circle, {
		//opacity: cycleBetween(5000, 0.5, 1, d3.easePolyInOut),
		strokeWidth: strokeWidth$,
		size: size$,
		ndashes: ndashes$,
		rotation: 0,//fastrotate,
		fill: color$,
		stroke: 'none',
		strokeLineCap: strokeLineCap$.map(tf('round')),
		touch: 0,
		boxShadow: checked$.map(R.not).map(Number).startWith(0).flatMapLatest(
			animate([
				'0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24)',
				'0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23)',
			], 200)
		),
		events: e => {
			var events = createEvents(e);
			events.onClick.subscribe(e => checked$.onNext(!checked$.getValue()));
			events.onSelectStart = e => {e.preventDefault()};
			events.onMount = e => {e.target.onselectstart = events.onSelectStart};
			return events.merge();
		},
	}, i$.map(R.add(1))),
	h(Circle, {
		strokeWidth: 0,
		size: size$,
		ndashes: 0,
		rotation: 0,//fastrotate,
		fill: darken('gray', 1.5),
		stroke: 'black',
		strokeLineCap: strokeLineCap$.map(tf('round')),
		touch: 0,
		boxShadow: '',
		textColor: 'white',
	}, i$.map(R.add(2))),
	h('br'),
	h(InputRange, {value: ndashes$, min: 0, max: 1, step: 0.1}),
	h(InputText, {type:'number',value: i$, min: 0, max: 60}),
	h(InputRange, {value: size$, min: 20, max: 60}),
	h(CheckBox, {value: strokeLineCap$}),
]);
Yolk.render(h('div', null, app4), document.getElementById('app4'));

