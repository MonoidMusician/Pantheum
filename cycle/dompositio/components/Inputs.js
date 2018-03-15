DPO.create({
	'InputRange': function InputRange({context, arguments, attributes, styles, events, children}) {
		attributes.type = attributes.type||'range';
		attributes.value = arguments.value$;
		events.onInput = e => arguments.value$.onNext(Number(e.target.value));
		return h('input', {attributes, styles, events}, children);
	},
});

function Status({props, children, createEventHandler: e}) {
	var CORRECT = 1;
	var INCORRECT = -1;
	var PENDING = 0;
	var HIDDEN = null;
	var time = arguments.delay||600;
	var colors = DPO.gather({
		correct: '#00E676',
		incorrect: '#D50000',
		pending: '#DFDFDF',
	}, arguments.palette, context.palette);

	var value$ = arguments.value$||new Rx.Subject();
	var status$ = value$.map(tf(PENDING, HIDDEN)).merge(
		throttle$(value$, time).filter(Boolean)
		.flatMapLatest(arguments.scorer)
		.map(tf(CORRECT, INCORRECT))
	).publish().refCount().startWith(HIDDEN);

	var rel = 0.7;
	var rel$ = new Rx.BehaviorSubject(rel);
	var size$ = rel$.map(R.multiply(40));
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

	return DPO.from(this).structure('div#wrapper', {
		'input': 0,
		'svg:svg#image': 'path#symbol',
	}).state({
		value$, status$,
		shape$, color$,
	}).attributes({
		// defaulted in
		'input': {
			placeholder: 'Type "hello"',
		},
		'#symbol': {
			d: shape$,
		},
	}).classes({
		'input': status$.map(s => {
			switch (s) {
				case CORRECT:   return 'valid'
				case INCORRECT: return 'invalid'
			} return 'validate'
		}),
	}).events({
		'input': {
			input: e => {value$.onNext(e.target.value)},
		},
	}).styles({
		'input': {
			width: size$.map(size => 'calc(100% - '+size+'px)'),
		},
		'#image': {
			display: 'inline-block',
			verticalAlign: rel$.map(R.multiply(-15)),
			visibility: status$.map(eq(HIDDEN, 'hidden', '')),
		},
		'#symbol': {
			size: size$,
			stroke: {
				width: w$,
				dasharray: dashes$,
				dashoffset: offset$,
				linecap: 'round',
			},
			fill: 'none',
		},
	});
}
