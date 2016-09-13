var {adj12, adj3, modify} = require('./inflection');
var {romanize} = require('./numerals');
var {getordinal} = require('./numbers');

// Calendar
var months = [
	adj12('jānuāri'),
	adj12('februāri'),
	adj12('mārti'),
	adj3('aprīl'),
	adj12('māi'),
	adj12('jūni'),
	adj12('jūli'),
	adj12('august'),
	modify(adj3('septembr'), [
		['nominative','singular','september']
	]),
	modify(adj3('octōbr'), [
		['nominative','singular','octōber']
	]),
	modify(adj3('novembr'), [
		['nominative','singular','november']
	]),
	modify(adj3('decembr'), [
		['nominative','singular','december']
	]),
];
var days = {
	'ides': {
		'nominative': 'īdūs',
		'accusative': 'īdūs',
		'ablative':   'īdibus',
		'dative':     'īdibus',
		'genitive':   'īduum',
		'vocative':   'īdūs'
	},
	'kalends': {
		'nominative': 'kalendæ',
		'accusative': 'kalendās',
		'ablative':   'kalendīs',
		'dative':     'kalendīs',
		'genitive':   'kalendārum',
		'vocative':   'kalendæ'
	},
	'nones': {
		'nominative': 'nōnæ',
		'accusative': 'nōnās',
		'ablative':   'nōnīs',
		'dative':     'nōnās',
		'genitive':   'nōnārum',
		'vocative':   'nōnæ'
	}
};
var weekdays = [
	'Sōlis', 'Lūnæ', 'Martis', 'Mercuriī', 'Iovis', 'Veneris', 'Saturnī'
];
var nones = function(month) {
	if (month == 2 || month == 4 || month == 6 || month == 9)
		return 7;
	return 5;
};
var getclass = function(date) {
	var m = date.getUTCMonth(), d = date.getUTCDate();
	if (d == 1) return 'kalends';
	if (d == nones(m)) return 'nones';
	if (d == nones(m)+8) return 'ides';
};
module.exports.romancalendar = function romancalendar(date) {
	var d = new Date(date);
	for (var o = 0; !getclass(d); ++o, d.setDate(d.getDate() + 1)) {}
	var c = getclass(d), m = d.getUTCMonth(), y = d.getUTCFullYear(), year = '', _case = 'accusative', pre = '';
	if (o == 0) _case = 'ablative';
	else if (o == 1) pre = 'prīdiē ';
	else {
		d.setDate(d.getDate() - 1);
		if (m == 2 && o > 5 && d.getUTCDate() == 29)
			o -= 1; // leap years have two VI ante Kal. Feb.
		pre = 'diē ' + getordinal(o+1, 'singular', 'ablative', 'masculine') + ' ante ';
	}
	if (y > 0) {
		year = ' annō dominī ' + romanize(y);
	}
	return weekdays[date.getUTCDay()] + ' ' + pre + Titlecase(days[c][_case]) + ' ' + Titlecase(months[m]['plural'][_case]['feminine']) + year;
};
function Titlecase(string) {
	return string.charAt(0).toUpperCase() + string.slice(1);
};
