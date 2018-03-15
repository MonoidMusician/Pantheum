var h = require('react-hyperscript');
var MaterialUI = require('material-ui');

var createClass = require('../../createClass');

var {romancalendar} = require('./lib/date');

var foundingofrome = new Date('-000752-04-21');
console.log(foundingofrome);

module.exports = createClass({
	displayName: 'page.tools.RomanNumerals',
	getInitialState() {
		return {
			date: new Date(),
			lat: 41.90278349999999, lon: 12.496365500000024,
		};
	},
	changeDate(event, newdate) {
		var date = new Date(this.state.date);
		newdate = new Date(newdate);
		date.setUTCFullYear(newdate.getUTCFullYear());
		date.setUTCMonth(newdate.getUTCMonth());
		date.setUTCDate(new Date(newdate).getUTCDate());
		this.setState({date});
	},
	toToday(event) {
		this.changeDate(event, new Date());
	},
	toFoundingOfRome(event) {
		this.changeDate(event, foundingofrome);
	},
	toMyLocation(event) {
		if ("geolocation" in navigator) {
			navigator.geolocation.getCurrentPosition((position) => {
				this.setState({lat: position.coords.latitude, lon: position.coords.longitude});
			});
		}
	},
	render: function renderRomanTime() {
		return h('article', [
			h('h2', "Roman time"),
			h('p', "Roman hours split the solar day (sunrise to sunset) into 12 equal parts. Find sunrise/sunset times for a date and location."),
			h('div', [
				h('h3', "Date"),
				romancalendar(this.state.date),
				h('br'),
				h(MaterialUI.DatePicker, {
					value: this.state.date,
					firstDayOfWeek: 0,
					id: 'datepicker',
					onChange: this.changeDate,
					locale: 'en-US',
					minDate: new Date('-000800'),
				}),
				' ', h(MaterialUI.FlatButton, {
					label: "Today",
					onTouchTap: this.toToday,
				}),
				' ', h(MaterialUI.FlatButton, {
					label: "Founding of Rome",
					onTouchTap: this.toFoundingOfRome,
				}),
			]),
			h('div', [
				h('h3', "Location"),
				' ', h(MaterialUI.TextField, {
					hintText: "Enter a latitude",
					floatingLabelText: "Latitude",
					value: this.lat,
					onChange: this.setLatitude,
				}),
				' ', h(MaterialUI.TextField, {
					hintText: "Enter a longitude",
					floatingLabelText: "Longitude",
					value: this.lon,
					onChange: this.setLongitude,
				}),
				' ', h(MaterialUI.FlatButton, {
					label: "My location",
					onTouchTap: this.toMyLocation,
					disabled: !window || !("navigator" in window) || !("geolocation" in window.navigator),
				}),
				h('div#map', {style: {height: 300}}),
			]),
		]);
	},
});


/*
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
var initMap = function() {
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
*/