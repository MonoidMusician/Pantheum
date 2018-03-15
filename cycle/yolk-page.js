var h = Yolk.h;
var O = Rx.Observable;

var getvalue = event => event.target.value;
var egetvalue = (e, ...v) => (eventh => [eventh, eventh.map(getvalue).startWith(...v)])(e());
var makeoption = selected$ => value => h('option', {
	value,
	selected: selected$.map(selected => selected === value)
}, value[0].toUpperCase() + value.substr(1));


function Counter({props, children, createEventHandler}) {
	var handlePlus = createEventHandler();
	var handleMinus = createEventHandler();
	var count$ = handlePlus.map(() => 1).merge(handleMinus.map(() => -1)).startWith(0).scan((x,y) => x+y);

	var title$ = props.title.map(title => "Awesome "+title);
	return h('div', null, [
		h('h1', null, title$),
		h('div', null, [
			h('button#plus', {onClick: handlePlus}, '+'),
			h('button#minus', {onClick: handleMinus}, '-'),
		]),
		h('div', null, ['count: ', count$]),
		children
	]);
}

function RomanNumerals({props, children, createEventHandler: e}) {
	var _arabic$ = e(), arabic$ = _arabic$.map(getvalue).startWith(231).map(number => ({number, numeral: romanize(number)||''}));
	var _roman$  = e(), roman$ = _roman$.map(getvalue).map(numeral => {
		numeral = numeral.toUpperCase();
		var number = deromanize(numeral);
		if (number === false) {
			if (numeral) return {numeral, number: 1, invalid: true}; // reject event
			else number = '';
		}
		return {numeral, number};
	});

	var force = 0;
	var numbers$ = arabic$.merge(roman$);
	var numbers$ = O.combineLatest(
		numbers$,
		numbers$.filter(a => !a.invalid),
		(a,b) => !a.invalid ? [a] : [a,b]
	).flatMap(a=>(a)); // flatten: [a] -> a, [a,b] -> a,b
	numbers$.arabic = numbers$.pluck('number');
	numbers$.roman = numbers$.pluck('numeral');
	var unicode$ = numbers$.roman.map(numeral => reromanize(numeral||''));

	var [_gender$, gender$] = egetvalue(e, 'masculine');
	var [_number$, number$] = egetvalue(e, 'singular');
	var [_case$,   case$  ] = egetvalue(e, 'nominative');

	var state$ = O.combineTemplate({
		_gender: gender$,
		_number: number$,
		_case: case$,
	}).combineLatest(numbers$, (...parts) => Object.assign({}, ...parts));
	state$ = state$.map(a => (a));
	var {cardinal, ordinal, distributive, adverbial} = new Proxy(state$.map(verbalize), {
		get: (target, key) => target.pluck(key)
	});

	return h('div', null, [
		h('h2', null, "Roman numerals"),
		h('div', {style: {display: 'flex'}}, [
			h('div', {style: {
				flexGrow: 1,
			}}, [
				h('input#arabic', {
					placeholder: "Arabic Number",
					type: 'number',
					min: 0, max: 499999,
					value: numbers$.arabic,
					onInput: _arabic$,
					style:{font:'inherit'},
				}),
				' = ',
				h('input#roman', {
					placeholder: "Roman Numeral",
					value: numbers$.roman,
					onInput: _roman$,
					style:{font:'inherit'},
				}),
				unicode$.map(unicode => [
					unicode && ' = ', unicode,
					unicode && ' = ', unicode.toLowerCase(),
				]),
				h('br'),
				h('select#gender', {onChange: _gender$}, ['feminine', 'masculine', 'neuter'].map(makeoption(gender$))),
				h('select#number', {onChange: _number$}, ['singular', 'plural'].map(makeoption(number$))),
				h('select#case', {onChange: _case$}, ['nominative', 'accusative', 'ablative', 'dative', 'genitive', 'vocative'].map(makeoption(case$))),
				h('br'), 'Cardinal: ', cardinal,
				h('br'), 'Ordinal: ', ordinal,
				h('br'), 'Distributive: ', distributive,
				h('br'), 'Adverbial: ', adverbial,
			]),
			h('div', {style:{
				flexShrink: 1,
			}}, h(Sidebar)),
		])
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

function transition$(input$, timing, easing, interpolate) {
	if (!easing)
		easing = R.identity;
	if (typeof timing === 'number')
		timing = R.always(timing);
	if (!interpolate)
		interpolate = typeof d3 === 'object' ? d3.interpolate : ((a, b) => (rel) => rel*(b-a)+a);
	return O.create(o => {
		var start, current, goal, inter, first, timed;
		var init = R.once(() => {
			if (!frameHandle) tick();
		});
		var subscriptions = [
			input$.subscribe(input => {
				init();
				if (typeof start === 'undefined')
					o.onNext(current = input);
				start = current;
				goal = input;
				if (start === goal) return;
				inter = interpolate(start, goal);
				timed = timing(start, goal);
				first = null;
			}, e => o.onError(e), () => o.onCompleted()),
			time$.subscribe(time => {
				var rel = 1;
				if (current === goal) return;
				if (time >= (first||(first=time))+timed) {
					o.onNext(current = goal);
					return;
				}
				rel = ((time-first)/timed);
				if (easing) rel = easing(rel, start, goal);
				o.onNext(current = inter(rel));
			}),
		];
		return new Rx.BinaryDisposable(...subscriptions);
	}, input$);
}
O.prototype.transition = function transition(...arg) {
	return transition$(this, ...arg);
};
O.prototype.motivate = function motivate(...arg) {
	return motivate$(this, ...arg);
};
function motivate$(input$, movement, continu) {
	return O.create(o => {
		var current, timer = new Timer(), newinput = NaN, lastoutput = NaN;
		var init = R.once(() => {
			if (!frameHandle) tick();
		});
		var subscriptions = [
			input$.subscribe(input => {
				init();
				newinput = input;
			}, e => o.onError(e), () => o.onCompleted()),
			time$.subscribe(time => {
				if (newinput === newinput) {
					if (!current) {
						current = Movement.Pause(newinput);
					} else {
						var timed = timer.get(time);
						if (timed !== timed) timed = 1;
						// TODO: account for gap between one ending and the next starting? eh
						current = movement(current.return(timed), newinput);
						timer.reset(time, current.duration);
						// timer.get(time) === 0
					}
					newinput = NaN;
				}
				if (!current) return;
				var timed = timer.get(time), output;
				if (timed !== timed && current.duration < Infinity) {
					output = current.next(current.duration).position;
					current = Movement.Pause(output);
				} else output = current.next(timed).position;
				if (output === output && output !== lastoutput) {
					//console.log(output, newinput, timer.get(time), current);
					o.onNext(lastoutput = output);
				}
			}),
		];
		return new Rx.BinaryDisposable(...subscriptions);
	}, input$);
}


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

var log = a => (console.log(a), a);

var renderd3 = (function() {
	var width = 400;
	var height = 200;

	var margin = {top: 30, right: 20, bottom: 30, left: 50},
		width = 600 - margin.left - margin.right,
		height = 300 - margin.top - margin.bottom;

	// Set the ranges
	var x = d3.scaleLinear().range([0, width]);
	var y = d3.scaleLinear().range([height, 0]);

	// Define the axes
	var xAxis = d3.axisBottom(x).ticks(5);
	var yAxis = d3.axisLeft(y).ticks(5);

	// Adds the svg canvas
	var svg = d3.select("body")
		.append("svg")
			.attr("width", width + margin.left + margin.right)
			.attr("height", height + margin.top + margin.bottom)
			.style('border', '1px solid gray')
		.append("g")
			.attr("transform", 
				  "translate(" + margin.left + "," + margin.top + ")");

	var v1 = svg.append('path').attr('fill', 'none').attr('stroke', 'green').attr('stroke-width', 2);
	var v2 = svg.append('path').attr('fill', 'none').attr('stroke', 'blue').attr('stroke-width', 2);
	var line = svg.append('path').attr('fill', 'none').attr('stroke', 'black').attr('stroke-width', 2);
	var draw = d3.line().x(([_x,_y]) => x(_x)).y(([_x,_y]) => y(_y));
	var xaxed, yaxed;

	return function(movement) {
		var data = [];
		var n = 500;
		var xs = R.range(0, n+1);
		for (let i in xs) {
			let x = i/n*movement.duration;
			let y = +movement.next(x).position;
			if (Number.isFinite(x) && Number.isFinite(y))
				data.push([x, y]);
		}
		if (!data.length) return;
		x.domain(d3.extent(data, R.nth(0)));
		y.domain(d3.extent(data, R.nth(1)));
		line.attr('d', draw(data));
		v1.attr('d', draw([data[0], [data[n][0], data[0][1] + data[n][0]*movement.next(0).velocity]]));
		v2.attr('d', draw([[data[0][0], data[n][1] - data[n][0]*movement.next(movement.duration).velocity], data[n]]));
		if (xaxed) xaxed.remove();
		if (yaxed) yaxed.remove();
		
		// Add the X Axis
		xaxed = svg.append("g")
			.attr("class", "x axis")
			.attr("transform", "translate(0," + height + ")")
			.call(xAxis);

		// Add the Y Axis
		yaxed = svg.append("g")
			.attr("class", "y axis")
			.call(yAxis);

	};
})();

function Movement(...props) {
	Object.assign(this, ...props);
	if (!this.duration) {
		console.log('invalid Movement: ', this);
		return Movement.Pause(this.next(0).position);
	}
	if (typeof this.position === 'number' && this.position !== this.next(0).position) {
		console.log('position did not match next(0)', this);
	}
	if (typeof this.goal === 'number' && this.goal !== this.next(1).position) {
		console.log('goal did not match next(1)', this);
	}
	renderd3(this);
}
Movement.Pause = function(position)  {
	return new Movement({
		type: 'pause',
		duration: Infinity,
		return(time) {
			return {
				position,
				velocity: 0,
			};
		},
		next(time) {
			return {position, velocity:0};
		},
	})
}
Movement.Relative = function(movement) {
	return new Movement({
		type: 'relative',
		duration: movement.duration,
		return(time) {
			return movement.return(time/movement.duration);
		},
		next(time) {
			return movement.next(time/movement.duration);
		}
	})
}
Movement.Linear = function({position, goal, duration}) {
	var displacement = goal-position;
	var velocity = displacement/duration;
	return new Movement.Relative({
		type: 'linear',
		duration,
		return(time) {
			return this.next(time);
		},
		next(time) {
			return {position:position+time*displacement, velocity};
		},
	})
}
Movement.Walk = function({position, goal, velocity}) {
	var displacement = goal-position;
	return new Movement.Relative({
		type: 'walk',
		duration: Math.abs(displacement)/velocity,
		return(time) {
			return this.next(time);
		},
		next(time) {
			return {position: position+time*displacement, velocity};
		},
	})
}
Movement.QuadIn = function({position, goal, velocity}) {
	var displacement = goal-position;
	return new Movement.Relative({
		type: 'quadin',
		duration: Math.abs(displacement)/velocity,
		return(time) {
			return this.next(time);
		},
		next(time) {
			return {
				position: position+time**2*displacement,
				velocity: 2*velocity*time,
			};
		},
	})
}
Movement.Accelerate = function({position, goal, velocity, acceleration, max_speed=Infinity}) {
	var displacement = goal-position;
	var accel = (goal > position ? acceleration : -acceleration);
	var inner_radical = velocity**2-2*accel*-displacement;
	if (Math.abs(inner_radical) < Number.EPSILON) inner_radical = 0;
	var radical = Math.sqrt(inner_radical);
	var duration = (-velocity+radical)/acceleration;
	//console.log(null, goal-position, velocity, radical, accel, duration);
	return new Movement({
		type: 'accelerate',
		duration,
		acceleration,
		return(time) {
			return this.next(time);
		},
		next(time) {
			return {
				position: position+velocity*time+accel*time**2/2,
				velocity: velocity+time*accel,
			};
		},
	})
}
Movement.Brake = function({position, goal, velocity}) {
	var displacement = goal-position;
	var duration = Math.abs(2*displacement/velocity);
	var acceleration = velocity/duration;
	return new Movement({
		type: 'brake',
		duration,
		return(time) {
			return this.next(time);
		},
		next(time) {
			return {
				position: position + velocity*time - acceleration/2*time**2,
				velocity: velocity - acceleration*time,
			}
		}
	})
}
Movement.DriveTo = function({position, goal, velocity, acceleration, max_speed}) {
	max_speed = Math.abs(max_speed);
	acceleration = Math.abs(acceleration);
	var speed = Math.abs(velocity);
	var displacement = goal-position;
	var sign = Math.sign(displacement);
	var accel = acceleration * sign;
	var max_velocity = max_speed * sign;
	var coast_t;
	//console.log({speed, displacement, sign, accel, max_velocity, max_speed, position, goal, velocity, acceleration});
	if (Math.sign(velocity) === sign && speed > 0) {
		var hard_decel_t = velocity/accel/2; // > 0
		var just_coast_t = displacement/velocity; // > 0
		if (just_coast_t < hard_decel_t) { // |a * Δx| < v**2/2
			//console.log('break:', just_coast_t, '<', hard_decel_t);
			return Movement.Brake({position, goal, velocity});
		}// else console.log('no brake:', just_coast_t, '>=', hard_decel_t);
	}// else console.log(Math.sign(velocity), sign, speed);
	var decel_t = Math.abs(max_velocity/acceleration);
	var decel_x = goal-decel_t**2*accel/2;
	var accel_t = velocity*sign < max_speed ? (max_velocity-velocity)/acceleration : 0;
	var accel_x = position+accel_t**2*accel/2+accel_t*velocity;
	if ((accel_x - decel_x)*sign > 0) {
		var dh = velocity**2/accel/2;
		var dt = velocity/accel;
		decel_t = Math.sqrt((dh+goal-position)/accel);
		accel_t = decel_t - dt;
		decel_x = goal-decel_t**2*accel/2;
		accel_x = position+accel_t**2*accel/2+accel_t*velocity;
		//console.log({dh, dt, decel_t, decel_x, accel_t, accel_x});
	}
	var coast_t = (decel_x - accel_x)/max_velocity;
	//console.log({decel_x, accel_t, accel_x, coast_t});
	var duration = accel_t + coast_t + decel_t;
	return new Movement({
		type: 'drive',
		duration,
		return(time) {
			return this.next(time);
		},
		next(time) {
			if (time < accel_t)
				return {
					position: position + velocity*time + accel/2*time**2,
					velocity: velocity + accel*time,
				}
			if (time <= accel_t + coast_t)
				return {
					position: accel_x + max_velocity*(time-accel_t),
					velocity: max_velocity,
				}
			return {
				position: goal - accel/2*(duration-time)**2,
				velocity: (duration-time) * accel,
			}
		}
	})
}
Movement.DriveThrough = function({position, goal, velocity, acceleration, max_speed}) {
	max_speed = Math.abs(max_speed);
	acceleration = Math.abs(acceleration);
	var speed = Math.abs(velocity);
	var displacement = goal-position;
	var sign = Math.sign(displacement);
	var accel = acceleration * Math.sign(max_speed-speed) * sign;
	var max_velocity = max_speed * sign;
	var accel_t = max_velocity-velocity && (max_velocity-velocity)/accel;
	var accel_x = position+accel_t**2*accel/2+accel_t*velocity;
	var coast_t = (goal - accel_x)/max_velocity;
	var duration = accel_t + coast_t;
	return new Movement({
		type: 'drive',
		duration,
		acceleration,
		return(time) {
			return this.next(time);
		},
		next(time) {
			if (time < accel_t)
				return {
					position: position + velocity*time + accel/2*time**2,
					velocity: velocity + accel*time,
				}
			return {
				position: accel_x + max_velocity*(time-accel_t),
				velocity: max_velocity,
			};
		}
	})
}
Movement.prototype.then = Movement.prototype.concat = function(...arg) {
	return Movement.Concat(this, ...arg);
}
Movement.Concat = function(...movements) {
	var l = movements.length;
	var I = t => R.clamp(0, l-1, Math.floor(t));
	var duration = R.sum(R.map(R.prop('duration'), movements));
	return new Movement({
		type: 'concat',
		movements,
		duration,
		return(time) {
			var i = 0, t = time;
			while (i < l-1 && t >= movements[i].duration)
			{ t -= movements[i].duration ; i++ }
			var b = movements[i];
			return b.return(t);
		},
		next(time) {
			var i = 0, t = time;
			while (i < l-1 && t >= movements[i].duration)
			{ t -= movements[i].duration ; i++ }
			var b = movements[i];
			return b.next(t);
		},
	})
}
Movement.prototype.bounce = function(loss=0.5, min=Infinity) {
	var acceleration = this.acceleration;
	var {position, velocity} = this.next(this.duration);
	velocity *= loss-1;
	var goal = position+(velocity**2)/2/acceleration;
	//console.log(position, goal, acceleration, velocity);
	var up = new Movement.Accelerate({position, goal, acceleration: -acceleration, velocity});
	var down = new Movement.Accelerate({position: goal, goal: position, acceleration, velocity: 0});
	if (!Number.isFinite(up.duration)) return this;
	//console.log(up, down);
	if (loss && Math.abs(velocity) > min) {
		return this.then(up, down.bounce(loss, min));
	} else return this.then(up, down);
}
function Timer() {
	this.start = NaN;
	this.duration = NaN;
}
Timer.prototype.reset = function resetTimer(time, duration) {
	if (duration < 1000/60)
		duration = 0;
	this.duration = duration;
	this.start = time;
}
Timer.prototype.get = function getTimer(time) {
	if (time > this.start + this.duration)
		return NaN;
	return (time-this.start);
}


var center = (...children) => h('td', {style:{textAlign:'center'}}, children);
var right = (...children) => h('td', {style:{textAlign:'right'}}, children);
function Sidebar({createEventHandler}) {
	var events = createEvents(createEventHandler);
	var open$ = events.onClick.startWith(1).scan(x => +!x);
	var react$ = events.active.combineLatest(open$, (a,b) => +!!a+2*b).startWith(2);
	var height$ = react$.map(R.prop(R.__, [1.5, 2.5, 17, 18])).motivate(function react({position, velocity}, goal) {
		var min = 1.5, max = 17, spread = max-min, tempus = 350, temp2 = tempus**2;
		var acceleration = spread/temp2*2; var max_speed = spread/tempus;
		if (goal === position) return Movement.Pause(goal);
		if (goal === 1.5 && position > goal+5)
			return Movement.DriveThrough({position, goal, velocity, acceleration, max_speed}).bounce(0.5, max_speed/4);
		return Movement.DriveTo({position, goal, velocity, acceleration, max_speed});
	}).publish().refCount();
	width$ = height$.map(h => h > 17 ? 5*Math.pow(17/h, 2) : h < 4.5 ? 3.8+(5-3.8)*Math.pow((h-1.5)/3, 1/2) : 5);
	var boxShadow$ = events.hover.map(Number).startWith(0).transition(200).map(
		d3.interpolate(
			'0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24)',
			/*/
			'0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23)'
			/*/
			'0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23)'
		)
	);/**/

	return h('div', {
		style: {
			float: 'right',
			overflow: 'hidden',
			height: height$.map(height => ''+height+'em'),
			maxWidth: width$.map(width => width+'em'),
			borderRadius: '2px',
			boxShadow: boxShadow$,
			padding: react$.map(v => '6px'),
			borderWidth: 1,
			borderStyle: 'solid',
			borderColor: height$.map(h => h > 0 && h==h ? 'transparent' : 'red'),
		}
	}, h('table', null, [
		h('tr', null, h('td', {
			style:{
				textAlign:'left'
			},
			colSpan: 2,
		}, h('a', events.merge({
			style: {
				/* Typography */
				fontFamily: 'Linux Biolinum',
				fontSize: '18px',
				color: '#CC3333',
				/* Internal padding */
				paddingTop: '0',
				paddingLeft: '8px',
				paddingRight: '8px',
				paddingBottom: '0',
				userSelect: 'none',
				WebkitUserSelect: 'none',
			},
		}), open$.map(open => open ? "Hide" : "Show")))),
		R.flatten(R.repeat([1,5], 5).map((v, i) => (idx => [v[0]*idx,v[1]*idx])(Math.pow(10, i)))).concat(100000).map(n => [right(n), center(reromanize(romanize(n)))]).map(tr => h('tr', null, tr))
	]))
}

Yolk.render(h(RomanNumerals, {title: 'Example'}), document.getElementById('app1'));
