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
<input id="arabic-number" placeholder="Arabic Number" value="<?= safe_get('number', $_GET) ?>">
= <input id="roman-number" placeholder="Roman Numeral">
<span id="output-uc"></span>
<span id="output-lc"></span>


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
}, function() {
	var val = $(this).val(), result = val && (deromanize(val) || "Error");
	$('#output-uc').text(val ? rr = '= '+reromanize(val.toUpperCase()) : '');
	$('#output-lc').text(val ? rr.toLowerCase() : '');
	if (result === "Error")
	{ $('#arabic-number').attr('placeholder', result); result = "" }
	else $('#arabic-number').attr('placeholder', "Arabic Number");
	$('#arabic-number').val(result);
}]
$('#arabic-number').on('keyup', change[0]).on('keydown', change[0]).trigger('keyup');
$('#roman-number') .on('keyup', change[1]).on('keydown', change[1]);
});
</script>
</article>




<article>
<h2>Roman time</h2>
<br>

<div class="floating" style="width: 230px;">
<h3>Location</h3>
<input class="medium" id="latitude" placeholder="Latitude"><input class="medium" id="longitude" placeholder="Longitude">
<button id="to-rome">In Rome</button>
<button id="to-london">In London</button>
<button id="hither">WhereAmI</button>
</div>
<div class="floating" style="width: 230px;">
<h3>Date</h3>
<input id="date" class="medium" type="text">
<button id="today">Today</button>
<button id="romefounding">Founding of Rome</button>
<div id="calendar" style="padding-top: 20px;"></div>
</div>
<div class="floating">
<h3>Results</h3>
Sunrise:
	<span id="sunrise"></span><br>
Solar noon:
	<span id="noon"></span><br>
Sunset:
	<span id="sunset"></span><br>
</div>

<br>

<div id="solarchart" style="height: 500px; width: 800px; clear: both;"></div>

<script>
$(function() {

var dateToStr = function(d) {
	return d3.time.format.iso(d).split("T")[0];
};

var date = new Date(), lat = 0, lon = 0;

// Calendar
var calendar = $('#calendar').calendar({date:date}).on('click', function() {
	$('input#date').val($(this).data('date'));
	date = new Date($(this).data('date'));
	displaytimes(date, +lat, +lon);
});
var update_date = function(d) {
	date = d; var s = dateToStr(date);
	$('input#date').val(s);
	calendar.data('date', s).update(date);
	displaytimes(date, +lat, +lon);
};
$('#today').on('click', function() {
	update_date(new Date());
});
$('#romefounding').on('click', function() {
	update_date(new Date("-000753-04-22"));
});
$('#date').on('change', function() {
	var d = new Date($(this).val());
	if (d)
		update_date(d);
});

var chart = d3.select('#solarchart').chart('Compose', function(data) {
	var scales = {
		x: {data: data, key: 'x'},
		y: {domain: [0, 24]}
	};

	var charts = [
		d3c.lines('results', {
			data: data,
			xScale: scales.x,
			yScale: scales.y
		})
	];

	var xAxis = d3c.axis('xAxis', {scale: scales.x});
	var yAxis = d3c.axis('yAxis', {scale: scales.y});
	var legend = d3c.legend({charts: ['results']});
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


function convertDateToUTC(date) { return new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds()); }
function displaytimes(date, lat, lon) {
	var times = SunCalc.getTimes(date, lat, lon);
	$('#latitude').val(lat); $('#longitude').val(lon);
	var f = d3.time.format.utc("%0I:%M %p (UTC)");
	$('#sunrise').text(f(times.sunrise));
	$('#sunset' ).text(f(times.sunset));
	$('#noon'   ).text(f(times.solarNoon));


	var data = [
	  {
		// required: values: [...]
		// optional: key, name
		key: 'sunrise',
		name: 'Sunrise (Prima Hora)', // (used in legend later)
		values: []
	  },
	  {
		key: 'sunset',
		name: 'Sunset (Duodecima Hora)',
		values: []
	  },
	  {
		key: 'noon',
		name: 'Noon (Sexta Hora)',
		values: []
	  },
	  {
		key: 'hr3',
		name: 'Tertia Hora',
		values: []
	  },
	  {
		key: 'hr9',
		name: 'Nona Hora',
		values: []
	  }
	];
	var year = date.getYear();
	var s = new Date(year, 0, 1), e = new Date(year, 12, 1);
	var i = 0; var avgnoon = 0;
	for (var d = new Date(s); d <= e; d.setDate(d.getDate() + 1)) {
		i++;
		var times = SunCalc.getTimes(d, lat, lon);
		var t1 = times.sunrise;
		data[0].values.push({x:i,y:t1.getUTCHours() + t1.getUTCMinutes()/60});
		var t2 = times.sunset;
		data[1].values.push({x:i,y:t2.getUTCHours() + t2.getUTCMinutes()/60});
		var t3 = times.solarNoon;
		data[2].values.push({x:i,y:t3.getUTCHours() + t3.getUTCMinutes()/60});
		var t4 = 0.5*t1 + 0.5 * t3; t4 = new Date(t4);
		data[3].values.push({x:i,y:t4.getUTCHours() + t4.getUTCMinutes()/60});
		var t5 = 0.5*t2 + 0.5 * t3; t5 = new Date(t5);
		data[4].values.push({x:i,y:t5.getUTCHours() + t5.getUTCMinutes()/60});
		avgnoon += times.solarNoon.getUTCHours();
	}
	avgnoon /= (i-1);
	var timezone = Math.round(36 - avgnoon);
	$.each(data, function(i,d) {
		$.each(d.values, function(j,pt) {
			pt.y += timezone;
			pt.y = pt.y % 24;
		});
	});

	chart.draw(data);
}
$('#latitude, #longitude').on('change', function() {
	lat = $('#latitude').val(), lon = $('#longitude').val();
	if (lat == +lat && lon == +lon)
		displaytimes(date, +lat, +lon);
});
$('#to-rome'  ).on('click', function() {displaytimes(date, lat = 41.9, lon = 12.5)}).trigger('click');
$('#to-london').on('click', function() {displaytimes(date, lat = 51.5, lon = -0.1)});
if ("geolocation" in navigator) {
	$('#hither').on('click', function() {
		navigator.geolocation.getCurrentPosition(function(position) {
			displaytimes(date, position.coords.latitude, position.coords.longitude);
		});
	});
} else {
	$('#hither').remove();
}


update_date(date);


});
</script>

</article>

