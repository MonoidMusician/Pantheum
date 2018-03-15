
var log = e => (console.log(e), e);

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
	if (typeof props.style === 'object') {
		var style = props.style;
		delete props.style;
	}
	for (let [setter, data] of [
		[set('style'), style],
		[set('attr'), props]
	]) {
		if (!data) continue;
		for (let key in data) {
			var value = data[key];
			var method = setter(kebabcase(key));
			if (O.isObservable(value))
				value.subscribe(method);
			else method(value);
		}
	}
}
function gradient(element, data) {
	var rel = Array.isArray(data) ? 1/(data.length-1) : 1;
	for (let i in data) {
		let d = data[i];
		if (typeof d !== 'object' || !('color' in d))
			d = {color: d, opacity: 1};
		D3bind(element.append('stop'), {
			stopColor: d.color,
			stopOpacity: d.opacity,
			style: {
			},
			offset: i*rel,
		});
	}
	return element;
}
function gradient$(element, data$) {
	return data$.map(data => gradient(element, data));
}

var svg = d3.select('#main');
var width = 400; var height = 400;
D3bind(svg, {
	width, height,
	style: {
		//backgroundColor: 'url(#gradient)',
	},
});
var defs = svg.select('defs');

var greengradient = gradient(
	defs.append('linearGradient').attr('id', 'greengradient'),
	['#1b5e20', '#7cb342']
);
// cycleBetween(2000).map(d3.interpolateCubehelix('#b3e5fc', 'red'))
var bluegradient = gradient(
	defs.append('linearGradient').attr('id', 'bluegradient'),
	['#b3e5fc', '#1e88e5']
);
var gradient = defs.append('linearGradient').attr('id', 'gradient');
//<linearGradient xmlns="http://www.w3.org/2000/svg" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" inkscape:collect="always" xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#linearGradient3755" id="linearGradient3761" x1="-60.591518" y1="256.13843" x2="339.40848" y2="256.13843" gradientUnits="userSpaceOnUse"/>
D3bind(gradient, {
	'xlink:href': '#greengradient',
	x1: "0",
	y1: "0",
	x2: "1",
	y2: "1",
});

Math.random(100);
var rand = (min=0,max=1) => (max-min)*Math.random()+min;
var randX = () => Math.random()*width;
var randY = () => Math.random()*height;


function rotate(cx, cy, x, y, angle) {
	var radians = Math.PI/180 * angle;
	var cos = Math.cos(radians);
	var sin = Math.sin(radians);
	x -= cx; y -= cy;
	return [cos*x + sin*y + cx, cos*y - sin*x + cy];
}
var urotate = (...arg) => rotate(0.5, 0.5, ...arg);

function rotategradient(origId, rotation) {
	if (!rotategradient.id) rotategradient.id = 0;
	if (arguments.length < 2) rotation = rand(0,360);
	var newId = `rotate${rotategradient.id++}${origId}`;
	var points = [[0, 0], [1, 1]];
	points = [urotate(...points[0], rotation), urotate(...points[1], rotation)];
	D3bind(defs.append('linearGradient'), {
		id: newId,
		'xlink:href': `#${origId}`,
		x1: points[0][0],
		x2: points[1][0],
		y1: points[0][1],
		y2: points[1][1],
	});
	return newId;
}

function circleAt({r, cx, cy}, clockwise) {
	var sign = clockwise ? -1 : 1;
	return `
M ${cx} ${cy}
m ${-sign*r} 0
a ${r} ${r} 0 1 0 ${sign* 2*r} 0
a ${r} ${r} 0 1 0 ${sign*-2*r} 0
`.trim().replace(/\s+/g, ' ');
}

function makeCircle(parent=svg, m=1) {
	var r = rand(20*Math.sqrt(m), 50)*m;
	var cx = randX();
	var cy = randY();
	var dr = rand(5,8);
	var dx = rand(-2,2);
	var dy = rand(-2,2);
	var arc = circleAt({r, cx, cy})+circleAt({r: r-dr, cx: cx+dx, cy: cy+dy}, true);
	parent.append('path')
		.attr('d', arc)
		.attr('fill', 'white')
		.attr('stroke', 'white')
		.attr('stroke-width', 0)
		.attr('fill-rule', 'evenodd');
	/*
	console.log(r, dr, cx, dx, cy, dy);
	D3bind(parent.append('circle'), {
		r, cx, cy,
		fill: 'white',
	});
	D3bind(parent.append('circle'), {
		r: r-dr,
		cx: cx+dx,
		cy: cy+dy,
		fill: 'black',
	});
	/**/
}

var background = svg.append('rect');
D3bind(background, {
	width, height,
	style: {
		fill: `#1b4e20`,
		filter: `url(#greenfilter)`,
		fillOpacity: 1,
	},
});

var mask = defs.append('mask').attr('id', 'circlemask');
var half = 1;
D3bind(svg.append('rect'), {
	width: width/half, height: height/half,
	fill: '#54758c',
	filter: `url(#bluefilter)`,
	mask: half === 1 ? `url(#circlemask)` : '',
});

var tile = function(generator, start, line, repeat) {
	var state = [start[0], start[1]];
	var i = -2;
	while (state[0] < width+repeat[0]) {
		state = [start[0] + i * repeat[0], start[1] + i * repeat[1]];
		while (state[0] < -2*line[0] || state[1] < -2*line[1]) {
			state[1] += line[1];
			state[0] += line[0];
		}
		while (state[1] <= height && state[0] <= width) {
			generator(state);
			state[1] += line[1];
			state[0] += line[0];
		}
		i += 1;
	}
};

//R.repeat(null, 10).map(() => makeCircle(mask));
//R.repeat(null, 10).map(() => makeCircle(mask, 2));
var parent = mask;
var circleGenerator = r => ([x,y]) => {
	var cx = x+r; var cy = y+r;
	var dr = rand(5,8);
	var dx = rand(-2,2);
	var dy = rand(-2,2);
	var arc = circleAt({r, cx, cy})+circleAt({r: r-dr, cx: cx+dx, cy: cy+dy}, true);
	parent.append('path')
		.attr('d', arc)
		.attr('fill', 'white')
		.attr('fill-rule', 'evenodd');
};
var tileCircle = (r, ...arg) => tile(circleGenerator(r), ...arg);
var R = 50;
tileCircle(R*2, [-R, -R*2], [R, R*2], [R*3, -R*3/5]);
tileCircle(R/3, [0, 0], [R/4, R], [R*3/4, -R*3/4]);
tileCircle(R/5, [-R, R], [R/2, R*3/2], [R*3/9, -R]);
