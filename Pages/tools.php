<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/common.php');
	sro('/PHP5/lib/PHPLang/db.php');
	sro('/PHP5/lib/PHPLang/display.php');
?>
<header>
	<h1>Tools</h1>
</header>
<article>
<h2>Roman numerals</h2>
<br>
<input id="arabic-number" placeholder="Arabic Number" type="number" value="<?= safe_get('number', $_GET) ?>">
= <input id="roman-number" placeholder="Roman Numeral">
<span id="output-uc"></span>
<span id="output-lc"></span>

<br>
<select id="gender" style="width: 150px;">
<option>masculine
<option>feminine
<option>neuter
</select>
<select id="number" style="width: 100px;">
<option>singular
<option>plural
</select>
<select id="case" style="width: 200px;">
<option>nominative
<option>accusative
<option>ablative
<option>dative
<option>genitive
<option>vocative
</select>
<br>
Cardinal: <span id="cardinal" class="format-word-la"></span>
<br>
Ordinal: <span id="ordinal" class="format-word-la"></span>

<script>
$(function() {


// From http://blog.stevenlevithan.com/archives/javascript-roman-numeral-converter
function romanize (num) {
	if (!+num)
		return false;
	var	digits = String(+num).split(""),
		key = ["","C","CC","CCC","CD","D","DC","DCC","DCCC","CM",
		       "","X","XX","XXX","XL","L","LX","LXX","LXXX","XC",
		       "","I","II","III","IV","V","VI","VII","VIII","IX"],
		roman = "",
		i = 3;
	while (i--)
		roman = (key[+digits.pop() + (i * 10)] || "") + roman;
	return Array(+digits.join("") + 1).join("M") + roman;
}

function deromanize (str) {
	var	str = str.toUpperCase(),
		validator = /^M*(?:D?C{0,3}|C[MD])(?:L?X{0,3}|X[CL])(?:V?I{0,3}|I[XV])$/,
		token = /[MDLV]|C[MD]?|X[CL]?|I[XV]?/g,
		key = {M:1000,CM:900,D:500,CD:400,C:100,XC:90,L:50,XL:40,X:10,IX:9,V:5,IV:4,I:1},
		num = 0, m;
	if (!(str && validator.test(str)))
		return false;
	while (m = token.exec(str))
		num += key[m[0]];
	return num;
}

// unicode!
function reromanize(str) {
	// also thanks to http://ingram-braun.net/public/programming/web/roman-numeral-unicode-form/
	$.each([
        ['MMMMM','ↁ'],
        ['MMMM','Ⅿↁ'],
        ['ↁↁ',    'ↂ'],
        ['ↂↂↂↂↂ',    'ↇ'],
        ['ↂↂↂↂ',    'ↂↇ'],
        ['ↇↇ',    'ↈ'],
        ['ↇↂↇ',    'ↂↈ'],
        ['ↁⅯↁ',    'Ⅿↂ'],
        ['ↂↁⅯↁ',    'ↂⅯↂ'],
		['M',   'Ⅿ'],
		['D',   'Ⅾ'],
		['C',   'Ⅽ'],
		['L',   'Ⅼ'],
		['IX',  'Ⅸ'],
		['XI',  'Ⅺ'],
		['XII', 'Ⅻ'],
		['X',   'Ⅹ'],
		['VIII','Ⅷ'],
		['VII', 'Ⅶ'],
		['VI',  'Ⅵ'],
		['IV',  'Ⅳ'],
		['V',   'Ⅴ'],
		['III', 'Ⅲ'],
		['II',  'Ⅱ'],
		['I',   'Ⅰ'],
	], function(_,r) {
		str = str.split(r[0]).join(r[1]);
	});
	return str;
}
function halfreromanize(str) {
	$.each([
        ['MMMMM','ↁ'],
        ['MMMM','Ⅿↁ'],
        ['ↁↁ',    'ↂ'],
        ['ↂↂↂↂↂ',    'ↇ'],
        ['ↂↂↂↂ',    'ↂↇ'],
        ['ↇↇ',    'ↈ'],
        ['ↇↂↇ',    'ↂↈ'],
        ['ↁⅯↁ',    'Ⅿↂ'],
        ['ↂↁⅯↁ',    'ↂⅯↂ'],
    ], function(_,r) {
		str = str.split(r[0]).join(r[1]);
	});
	return str;
}

var change = [function() {
	var val = $(this).val(), result = val && (romanize(val) || "Error"), rr = '= ' + reromanize(result);
	$('#output-uc').text((!result || result == "Error") ? "" : rr);
	$('#output-lc').text((!result || result == "Error") ? "" : rr.toLowerCase());
	if (result === "Error")
	{ $('#roman-number').attr('placeholder', result); result = "" }
	else $('#roman-number').attr('placeholder', "Roman Numeral");
	$('#roman-number') .val(halfreromanize(result));
	verbalize();
}, function() {
	var val = $(this).val(), result = val && (deromanize(val) || "Error");
	$('#output-uc').text(val ? rr = '= '+reromanize(val.toUpperCase()) : '');
	$('#output-lc').text(val ? rr.toLowerCase() : '');
	if (result === "Error")
	{ $('#arabic-number').attr('placeholder', result); result = "" }
	else $('#arabic-number').attr('placeholder', "Arabic Number");
	$('#arabic-number').val(result);
	verbalize();
}]
$('#arabic-number').on('keyup', change[0]).on('keydown', change[0]).on('change', change[0]);
$('#roman-number') .on('keyup', change[1]).on('keydown', change[1]);



$('select').select2({minimumResultsForSearch: Infinity});
var adj12 = function(b) {
	return {
		'singular': {
			'nominative': {
				'masculine': b+'us',
				'feminine':  b+'a',
				'neuter':    b+'um'
			},
			'accusative': {
				'masculine': b+'um',
				'feminine':  b+'am',
				'neuter':    b+'um'
			},
			'ablative': {
				'masculine': b+'ō',
				'feminine':  b+'ā',
				'neuter':    b+'ō'
			},
			'dative': {
				'masculine': b+'ō',
				'feminine':  b+'æ',
				'neuter':    b+'ō'
			},
			'genitive': {
				'masculine': b+'ī',
				'feminine':  b+'æ',
				'neuter':    b+'ī'
			},
			'vocative': {
				'masculine': b+'e',
				'feminine':  b+'a',
				'neuter':    b+'um'
			}
		},
		'plural': {
			'nominative': {
				'masculine': b+'ī',
				'feminine':  b+'æ',
				'neuter':    b+'a'
			},
			'accusative': {
				'masculine': b+'ōs',
				'feminine':  b+'ās',
				'neuter':    b+'a'
			},
			'ablative': {
				'masculine': b+'īs',
				'feminine':  b+'īs',
				'neuter':    b+'īs'
			},
			'dative': {
				'masculine': b+'īs',
				'feminine':  b+'īs',
				'neuter':    b+'īs'
			},
			'genitive': {
				'masculine': b+'ōrum',
				'feminine':  b+'ārum',
				'neuter':    b+'ōrum'
			},
			'vocative': {
				'masculine': b+'ī',
				'feminine':  b+'æ',
				'neuter':    b+'a'
			}
		}
	}
};
var modify = function(forms, list) {
	$.each(list, function(_,l) {
		var f = forms, v = l.pop(), i;
		while (l.length > 1)
			f = f[l.pop()];
		i = l.pop();
		if (typeof f[i] !== 'object')
			f[i] = v;
		else {
			$.each(f[i], function(k, _) {
				f[i][k] = v;
			});
		}
	});
	return forms;
};
var cardinals = {
	0: 'nihil',
	1: modify(adj12('ūn'), [
		['dative','singular','ūnī'],
		['genitive','singular','ūnīus'],
	]),
	2: {
		'nominative': {
			'masculine': 'duo',
			'feminine':  'duæ',
			'neuter':    'duo',
		},
		'accusative': {
			'masculine': 'duōs',
			'feminine':  'duās',
			'neuter':    'duo',
		},
		'ablative': {
			'masculine': 'duōbus',
			'feminine':  'duābus',
			'neuter':    'duōbus',
		},
		'dative': {
			'masculine': 'duōbus',
			'feminine':  'duābus',
			'neuter':    'duōbus',
		},
		'genitive': {
			'masculine': 'duōrum',
			'feminine':  'duārum',
			'neuter':    'duōrum',
		},
		'vocative': {
			'masculine': 'duo',
			'feminine':  'duæ',
			'neuter':    'duo',
		},
	},
	3: {
		'masculine': 'trēs',
		'feminine':  'trēs',
		'neuter':    'tria',
		'ablative': 'tribus',
		'dative':   'tribus',
		'genitive': 'trium',
	},
	4: 'quattuor', 5: 'quīnque', 6: 'sex', 7: 'septem', 8: 'octō', 9: 'novem',
	10: 'decem', 11: 'ūndecim', 12: 'duodecim', 13: 'tredecim', 14: 'quattuordecim',
	15: 'quīndecim', 16: 'sēdecim', 17: 'septendecim',
	20: 'vīgintī', 30: 'trīgintā', 40: 'quadrāgintā', 50: 'quīnquāgintā',
	60: 'sexāgintā', 70: 'septuāgintā', 80: 'octōgintā', 90: 'nōnāgintā', 100: 'centum',
	200: adj12('ducent')['plural'],
	300: adj12('trecent')['plural'],
	400: adj12('quadrigent')['plural'],
	500: adj12('quīngent')['plural'],
	600: adj12('sescent')['plural'],
	700: adj12('septingent')['plural'],
	800: adj12('octingent')['plural'],
	900: adj12('nōngent')['plural'],
};
var ordinals = {
	1: adj12('prīm'),
	2: adj12('secund'),
	3: adj12('terti'),
	4: adj12('quārt'),
	5: adj12('quīnt'),
	6: adj12('sext'),
	7: adj12('septim'),
	8: adj12('octāv'),
	9: adj12('nōn'),
	10: adj12('decim'),
	11: adj12('ūndecim'),
	12: adj12('duodecim'),
	20: adj12('vīcēsim'),
	30: adj12('trīcēsim'),
	40: adj12('quadrāgēsim'),
	50: adj12('quīnquāgēsim'),
	60: adj12('sexāgēsim'),
	70: adj12('septuāgēsim'),
	80: adj12('octōgēsim'),
	90: adj12('nōnāgēsim'),
	100: adj12('centēsim'),
};
var verbalize = function() {
	var n = +$('#arabic-number').val(), cardinal, ordinal;
	var ones = n % 10, tens = n - ones;
	var number = $('#number').val();
	var _case =  $('#case').val();
	var gender = $('#gender').val();
	var parse = function(verb) {
		if (typeof verb === 'object' && number in verb)
			verb = verb[number];
		if (typeof verb === 'object' && _case  in verb)
			verb = verb[_case];
		if (typeof verb === 'object' && gender in verb)
			verb = verb[gender];
		if (typeof verb === 'object')
			verb = null;
		return verb;
	};
	var combine = function() {
		var res = "";
		for(var i = 0; i < arguments.length; i++) {
			var a = arguments[i];
			if (!a && a !== '') return;
			res += a;
		}
		return res;
	}
	if (n > 0) {
		ordinal  = parse(ordinals [n]);
		cardinal = parse(cardinals[n]);

		if (n > 10) {
			if (!cardinal && n > 20 && ones in cardinals && tens in cardinals) {
				cardinal = parse(cardinals[ones]);
				if (cardinal) cardinal = cardinals[tens] + ' ' + cardinal;
			}
			if (!ordinal && ones in ordinals && tens in ordinals) {
				ordinal = parse(ordinals[ones]);
				var o1 = ordinal;
				ordinal = parse(ordinals[tens]);
				if (ordinal && o1)
					if (n < 20) ordinal = o1 + ' ' + ordinal;
					else ordinal += ' ' + o1;
			}
			var c, o;
			if ((n % 10) == 8) {
				c = combine('duodē',parse(cardinals[n+2]));
				o = combine('duodē',parse(ordinals [n+2]));
			} else if ((n % 10) == 9) {
				c = combine('ūndē',parse(cardinals[n+1]));
				o = combine('ūndē',parse(ordinals [n+1]));
			}
			if (c) {
				if (cardinal) cardinal = cardinal + ' / ' + c;
				else cardinal = c;
			}
			if (o) {
				if (ordinal) ordinal = ordinal + ' / ' + o;
				else ordinal = o;
			}
		}
	}
	if (!cardinal)
		cardinal = '';
	if (!ordinal)
		ordinal = '';
	$('#cardinal').text(cardinal).attr('data-original-word0', '');
	$('#ordinal').text(ordinal).attr('data-original-word0', '');
	la_ipa.format();
};
$('#case, #gender, #number').on('change', verbalize);

$('#arabic-number').trigger('keyup');
});
</script>
</article>




<article>
<h2>Roman time</h2>
<p>Roman hours split the solar day (sunrise to sunset) into 12 equal parts. Find sunrise/sunset times for a date and location.

<div>
	<h3>Date</h3>
	<input id="date" class="medium" type="text">
	<button id="today">Today</button>
	<button id="romefounding">Founding of Rome</button>
	<div id="calendar" style="padding-top: 20px;"></div>
</div>

<div>
	<h3>Location</h3>
	<input id="place">
	<style>
		#place {
			background-color: #fff;
			font-family: Roboto;
			font-size: 15px;
			font-weight: 300;
			margin-left: 12px;
			padding: 0 11px 0 13px;
			text-overflow: ellipsis;
			width: 300px;
			margin-top: 10px;
			border: 1px solid transparent;
			border-radius: 2px 0 0 2px;
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			height: 32px;
			outline: none;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
		}

		input#place:focus:not(.select2-search__field) {
			border-color: #4d90fe;
		}
	</style>
	<input class="medium" id="latitude" placeholder="Latitude"><input class="medium" id="longitude" placeholder="Longitude">
	<button id="to-rome">In Rome</button>
	<button id="to-london">In London</button>
	<button id="hither">WhereAmI</button>

	<div id="map" style="height: 300px;"></div>
</div>

<div>
	<h3>Timezone</h3>
	UTC+<input id="timezone" class="small" value="0" min="-12" max="12" type="number">
	<button id="utc">UTC</button>
	<button id="localtime">My Timezone</button>
	<button id="loctime">By location</button>
</div>

<div>
	<h3>Results</h3>
	Sunrise:
		<span id="sunrise"></span><br>
	Solar noon:
		<span id="noon"></span><br>
	Sunset:
		<span id="sunset"></span><br>
	Roman hour: <span id="romanhour"></span> hours / <span id="romanminutes"></span> minutes
</div>

<br>

<div id="solarchart" style="height: 500px; width: 800px; clear: both;"></div>

<script>
var initMap;
$(function() {

var dateToStr = function(d) {
	return d3.time.format.iso(d).split("T")[0];
};

var date = new Date(), lat = 0, lng = 0, timezone = 0;
date.setUTCDate(date.getDate());



// Output graph
var chart = d3.select('#solarchart').chart('Compose', function(data) {
	var scales = {
		x: {data: data.data, key: 'x'},
		y: {domain: data.range}
		//y: {data: data, key: 'y'},
	};

	var charts = [
		d3c.lines('times', {
			data: data.data,
			xScale: scales.x,
			yScale: scales.y
		}),
		d3c.lines('dates', {
			data: data.dates,
			xScale: scales.x,
			yScale: scales.y
		}),
	];

	var xAxis = d3c.axis('xAxis', {scale: scales.x});
	var yAxis = d3c.axis('yAxis', {scale: scales.y});
	var legend = d3c.legend({charts: ['times','dates']});
	var title = d3c.title('Sun times throughout the year');
	var xAxisTitle = d3c.axisTitle('Day');
	var yAxisTitle = d3c.axisTitle('Hour');

	return [
		title,
		[yAxisTitle, yAxis, d3c.layered(charts), legend],
		xAxis,
		xAxisTitle
	];
}).width(800).height(500);


function displaytimes(date, lat, lng) {
	var times = SunCalc.getTimes(date, lat, lng);
	$('#latitude').val(lat); $('#longitude').val(lng);
	var _f = d3.time.format.utc("%0H:%M"), f = function(d) {
		if (isNaN(d.getTime())) return "None";
		d = new Date(d);
		d.setHours(d.getHours() + timezone);
		return _f(d);
	};
	function fix(t, d, h) {
		if (!isNaN(t.getTime())) return t;
		t = new Date(d);
		t.setUTCHours(h);
		return t;
	}
	function getT(t, prev) {
		if (Array.isArray(prev)) {
			if (!prev.length) prev = getT(t);
			else prev = prev[prev.length-1];
		}
		if (typeof prev === 'object')
			prev = prev.y;
		if (isNaN(t.getTime()))
			return prev;
		var t = t.getUTCHours() + t.getUTCMinutes()/60 + t.getUTCSeconds()/60/60;
		if (prev === undefined) return t;
		if (t > prev+6) t -= 24;
		else if (t < prev-6) t += 24;
		return t;
	}
	$('#sunrise').text(f(times.sunrise));
	$('#sunset' ).text(f(times.sunset));
	$('#noon'   ).text(f(times.solarNoon));
	var t = getT(times.sunset) - getT(times.sunrise);
	if (t < 0) t += 24;
	$('#romanhour'   ).text((t/12).toFixed(2));
	$('#romanminutes').text((t/12*60).toFixed(0));


	var data = [
	  {
		// required: values: [...]
		// optional: key, name
		key: 'sunrise',
		name: 'Sunrise (Prima Hora)', // (used in legend later)
		values: []
	  },
	  {
		key: 'hr3',
		name: 'Tertia Hora',
		values: []
	  },
	  {
		key: 'noon',
		name: 'Noon (Sexta Hora)',
		values: []
	  },
	  {
		key: 'hr9',
		name: 'Nona Hora',
		values: []
	  },
	  {
		key: 'sunset',
		name: 'Sunset (Duodecima Hora)',
		values: []
	  },
	];
	var dates = [
	  {
	  	key: 'today',
	  	name: 'Selected Date',
	  	values: []
	  },
	  {
	  	key: 'solstice1',
	  	name: 'Solstice',
	  	values: []
	  },
	  {
	  	key: 'solstice2',
	  	name: 'Solstice',
	  	values: []
	  },
	  {
	  	key: 'equinox1',
	  	name: 'Equinox',
	  	values: []
	  },
	  {
	  	key: 'equinox2',
	  	name: 'Equinox',
	  	values: []
	  },
	];
	var year = date.getFullYear(), today;
	var s = new Date(year, 0, 1), e = new Date(year, 12, 1);
	var i = 0; var avgnoon = 0;
	var _t4 = undefined, _t5 = undefined;
	var days = [];
	for (var d = new Date(s); d <= e; d.setDate(d.getDate() + 1)) {
		i++;
		var times = SunCalc.getTimes(d, lat, lng);
		var t1 = getT(times.sunrise, data[0].values);
		data[0].values.push({x:i,y:t1});
		var t2 = getT(times.sunset, data[4].values);
		if (t2 < t1) t2 += 24;
		data[4].values.push({x:i,y:t2});
		var t3 = getT(times.solarNoon, data[2].values);
		if (t3 < t1) t3 += 24;
		data[2].values.push({x:i,y:t3});
		var t4 = 0.5 * t1 + 0.5 * t3;
		if (t4 < t1) t4 += 24;
		data[1].values.push({x:i,y:t4});
		var t5 = 0.5 * t2 + 0.5 * t3;
		if (t5 < t1) t5 += 24;
		data[3].values.push({x:i,y:t5});
		avgnoon += getT(times.solarNoon);
		if (d.getMonth() == date.getMonth() && d.getDate() == date.getDate()) {
			today = dates[0];
			today.values.push({x:i,y:t1});
			today.values.push({x:i,y:t2});
		}
		_t4 = t4; _t5 = t5;
		days.push([d,t1,t2,t3,t4,t5,t2-t1,Math.abs(t2-t1-12)]);
	}
	var maxlux = 0, minlux = 0, eq1 = 0, eq2 = 0;
	$.each(days, function(i,day) {
		var lux = day[6], equi = day[7];
		if (lux > days[maxlux][6]) maxlux = i;
		else if (maxlux != 0 && lux == days[maxlux][6]) {
			dates[1].values.push({'x':maxlux,'y':days[maxlux][1]});
			dates[1].values.push({'x':maxlux,'y':days[maxlux][2]});
		}
		if (lux < days[minlux][6]) minlux = i;
		else if (maxlux != 0 && lux == days[minlux][6])	{
			dates[2].values.push({'x':minlux,'y':days[minlux][1]});
			dates[2].values.push({'x':minlux,'y':days[minlux][2]});
		}
		if (i <= days.length/2) {
			if (equi < days[eq1][7]) eq1 = i;
			else if (eq1 != 0 && lux == days[eq1][6])	{
				dates[3].values.push({'x':eq1,'y':days[eq1][1]});
				dates[3].values.push({'x':eq1,'y':days[eq1][2]});
			}
		} else {
			if (equi < days[eq2][7]) eq2 = i;
			else if (eq2 != 0 && lux == days[eq2][6])	{
				dates[4].values.push({'x':eq2,'y':days[eq2][1]});
				dates[4].values.push({'x':eq2,'y':days[eq2][2]});
			}
		}
	});
	dates[1].values.push({'x':maxlux,'y':days[maxlux][1]});
	dates[1].values.push({'x':maxlux,'y':days[maxlux][2]});
	dates[2].values.push({'x':minlux,'y':days[minlux][1]});
	dates[2].values.push({'x':minlux,'y':days[minlux][2]});
	dates[3].values.push({'x':eq1,'y':days[eq1][1]});
	dates[3].values.push({'x':eq1,'y':days[eq1][2]});
	dates[4].values.push({'x':eq2,'y':days[eq2][1]});
	dates[4].values.push({'x':eq2,'y':days[eq2][2]});
	avgnoon /= (i-1);
	var tz = Math.round(12 - avgnoon);
	$.each(data, function(i,d) {
		$.each(d.values, function(j,pt) {
			pt.y += timezone;
		});
	});
	$.each(dates, function(i,d) {
		$.each(d.values, function(j,pt) {
			pt.y += timezone;
		});
	});

	chart.draw({data:data, dates:dates, range:[-tz+timezone,-tz+timezone+24]});
}







// Timezone
$('#utc').on('click', function() {
	$('#timezone').val(0).trigger('change');
});
$('#localtime').on('click', function() {
	$('#timezone').val(-(new Date()).getTimezoneOffset()/60).trigger('change');
});
$('#loctime').on('click', function() {
	$.get('https://maps.googleapis.com/maps/api/timezone/json', {
		location: lat+','+lng,
		timestamp: date.getTime()/1000,
		key: 'AIzaSyB2kxU6e0-_Wqgaac-IJ7ZI5X1gEaG6IsE',
	}).done(function(data) {
		console.log(data);
		if (data.status != "OK")
			alert(data.status);
		$('#timezone').val((data.dstOffset + data.rawOffset)/60/60).trigger('change');
	});
});
$('#timezone').on('change', function() {
	timezone = +$(this).val();
	displaytimes(date, lat, lng);
});

// Calendar
var calendar = $('#calendar').calendar({date:date}).on('click', function() {
	$('input#date').val($(this).data('date'));
	date = new Date($(this).data('date'));
	displaytimes(date, +lat, +lng);
});
var update_date = function(d) {
	date = d; var s = dateToStr(date);
	$('input#date').val(s);
	calendar.data('date', s).update(date);
	displaytimes(date, +lat, +lng);
};
$('#today').on('click', function() {
	var d = new Date();
	d.setUTCDate(d.getDate());
	update_date(d);
});
$('#romefounding').on('click', function() {
	update_date(new Date("-000753-04-21"));
});
$('#date').on('change', function() {
	var d = new Date($(this).val());
	if (d)
		update_date(d);
});

// Location
function update_map(latitude, longitude, searching) {
	lat = +latitude, lng = +longitude;
	$('#latitude').val(lat);
	$('#longitude').val(lng);
	if (typeof map != 'undefined') {
		map.setCenter({lat:lat,lng:lng});
		marker.setPosition({lat:lat,lng:lng});
		if (searching != true) {
			if (!searching) searching = '';
			$(input).val(searching);
		}
	}
	displaytimes(date, lat, lng);
}
$('#latitude, #longitude').on('change', function() {
	lat = $('#latitude').val(), lng = $('#longitude').val();
	if (lat == +lat && lng == +lng)
		update_map(lat, lng);
});
$('#to-rome'  ).on('click', function() {update_map(41.90278349999999,12.496365500000024,"Rome");}).trigger('click');
$('#to-london').on('click', function() {update_map(51.5073509,-0.12775829999998223,"London");});
if ("geolocation" in navigator) {
	$('#hither').on('click', function() {
		navigator.geolocation.getCurrentPosition(function(position) {
			update_map(position.coords.latitude, position.coords.longitude);
		});
	});
} else {
	$('#hither').remove();
}


update_date(date);

var map, marker, input, searchBox;
initMap = function() {
	map = new google.maps.Map(document.getElementById('map'), {
		center: {lat: lat, lng: lng},
		zoom: 5
	});
    marker = new google.maps.Marker({
		position: {lat:lat,lng:lng},
		map: map, draggable: true,
		title: "Drag me!",
	});
	google.maps.event.addListener(marker, 'dragend', function(event) {
        update_map(marker.getPosition().lat(),marker.getPosition().lng());
    });

	input = document.getElementById('place');
	searchBox = new google.maps.places.SearchBox(input);
	map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

	// Bias the SearchBox results towards current map's viewport.
	map.addListener('bounds_changed', function() {
		searchBox.setBounds(map.getBounds());
	});

	var markers = [];
	// Listen for the event fired when the user selects a prediction and retrieve
	// more details for that place.
	searchBox.addListener('places_changed', function() {
		var places = searchBox.getPlaces();

		if (places.length == 0) {
			return;
		}

		// Clear out the old markers.
		markers.forEach(function(marker) {
			marker.setMap(null);
		});
		markers = [];

		if (places.length == 1) {
			var loc = places[0].geometry.location;
	        update_map(loc.lat(),loc.lng(),true);
			return;
		}

		// For each place, get the icon, name and location.
		var bounds = new google.maps.LatLngBounds();
		places.forEach(function(place) {
			var icon = {
				url: place.icon,
				size: new google.maps.Size(71, 71),
				origin: new google.maps.Point(0, 0),
				anchor: new google.maps.Point(17, 34),
				scaledSize: new google.maps.Size(25, 25)
			};

			// Create a marker for each place.
			markers.push(new google.maps.Marker({
				map: map,
				icon: icon,
				title: place.name,
				position: place.geometry.location
			}));

			if (place.geometry.viewport) {
				// Only geocodes have viewport.
				bounds.union(place.geometry.viewport);
			} else {
				bounds.extend(place.geometry.location);
			}
		});
		map.fitBounds(bounds);
	});
}

});

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB2kxU6e0-_Wqgaac-IJ7ZI5X1gEaG6IsE&callback=initMap&libraries=places" async defer></script>

</article>

