//var {adj12, adj3, modify} = require('./inflection');

var milia = {
	'nominative': 'mīlia',
	'accusative': 'mīlia',
	'vocative': 'mīlia',
	'dative': 'mīlibus',
	'ablative': 'mīlibus',
	'genitive': 'mīlium',
};
var cardinals = {
	'': 'quot',
	0: adj12('nūll'),
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
	20: 'vīgintī', 100: 'centum',
	1000: 'mīlle',
};
var ordinals = {
	'': adj12('quot'),
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
	20: adj12('vīcē(n)sim'),
	30: adj12('trīcē(n)sim'),
	100: adj12('centē(n)sim'),
	1000: adj12('mīllē(n)sim'),
};
var distributives = {
	'': adj12('quotēn'),
	1: adj12('singul'),
	2: adj12('bīn'),
	3: adj12('tern'),
	4: adj12('quatern'),
	5: adj12('quīn'),
	6: adj12('sēn'),
	7: adj12('septēn'),
	8: adj12('octōn'),
	9: adj12('novēn'),
	10: adj12('dēn'),
	11: adj12('ūndēn'),
	12: adj12('duodēn'),
	16: adj12('sēdēn'),
	20: adj12('vīcēn'),
	30: adj12('trīcēn'),
	100: adj12('centēn'),
	1000: adj12('mīllēn'),
};
var adverbials = {
	'': 'quotiē(n)s',
	1: 'semel',
	2: 'bis',
	3: 'ter',
	4: 'quater',
	5: 'quīnquiē(n)s',
	6: 'sexiē(n)s',
	7: 'septiē(n)s',
	8: 'octiē(n)s',
	9: 'noviē(n)s',
	10: 'deciē(n)s',
	11: 'ūndeciē(n)s',
	12: 'duodeciē(n)s',
	13: 'terdeciē(n)s',
	14: 'quaterdeciē(n)s',
	15: 'quīndeciē(n)s',
	16: 'sēdeciē(n)s',
	20: 'vīciē(n)s',
	30: 'trīciē(n)s',
	1000: 'mīlliē(n)s',
};
var tens = [
	'vī', 'trī', 'quadrā', 'quīnquā',
	'sexā', 'septuā', 'octō', 'nōnā'
];
var hundreds = [
	'', 'du', 'tre', 'quadrin',
	'quīn', 'ses', 'septin', 'octin', 'nōn'
];
var i;
i = 20;
for (let pre of tens) {
	if (!(i in cardinals))
		cardinals    [i] = pre+'gintā';
	if (!(i in ordinals))
		ordinals     [i] = adj12(pre+'gē(n)sim');
	if (!(i in distributives))
		distributives[i] = adj12(pre+'gēn');
	if (!(i in adverbials))
		adverbials   [i] = pre+'giē(n)s';
	i += 10;
}
i = 100;
for (let pre of hundreds) {
	var c = pre.endsWith('n') ? 'g':'c';
	if (!(i in cardinals))
		cardinals    [i] = adj12(pre+c+'ent')['plural'];
	if (!(i in ordinals))
		ordinals     [i] = adj12(pre+c+'entē(n)sim');
	if (!(i in distributives))
		distributives[i] = adj12(pre+c+'ēn');
	if (!(i in adverbials))
		adverbials   [i] = pre+c+'entiē(n)s';
	i += 100;
};
var decs = function(n,pad) {
	var ret = [];
	while (n) {
		ret.push(n % 10);
		n = Math.floor(n/10);
	}
	while (pad && ret.length < pad)
		ret.push(0);
	return ret;
};
var parse = function(verb, number, _case, gender) {
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
var milparse = function(_case) {
	return function(verb){return parse(verb, 'plural', _case, 'neuter')};
};
var combine = function() {
	var res = "";
	for(var i = 0; i < arguments.length; i++) {
		var a = arguments[i];
		if (!a && a !== '') return;
		res += a;
	}
	return res;
};
var getter = function(generator) {
	return function(n, number, _case, gender, multiple) {
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
		var mparse = milparse(_case);
		return generator(n, parse, mparse, multiple);
	}
};


var _cardinal = function(n, parse, milparse, multiple) {
	var dec =  decs(n,4), cardinal, c;
	if (n > 0 || n === '') {
		cardinal = parse(cardinals[n]);

		if (n > 10) {
			if (!cardinal && n > 20) {
				if (n < 100)
					cardinal = combine(parse(cardinals[dec[1]*10]), ' ', parse(cardinals[dec[0]]));
				else if (n < 1000)
					cardinal = combine(parse(cardinals[dec[2]*100]), ' et ', _cardinal(n%100, parse, multiple));
				else {
					var et = '';
					if (dec[0] || dec[1] || dec[2])
						et = combine(' et ', dec[2]?parse(cardinals[dec[2]*100]):'', ' ', dec[0]||dec[1]?_cardinal(n%100, parse):'');

					if (n >= 2000)
						cardinal = combine(_cardinal(Math.floor(n/1000), milparse), ' ', parse(milia), et, ' (+GEN)');
					else cardinal = combine(cardinals[1000], et);
				}
			}

			if (n < 100 && (n % 10) == 8 && n != 98)
				c = combine('duodē',parse(cardinals[n+2]));
			else if (n < 100 && (n % 10) == 9)
				c = combine('ūndē',parse(cardinals[n+1]));

			if (c && cardinal != c)
				if (cardinal && multiple) cardinal = cardinal + ' / ' + c;
				else cardinal = c;
		}
	}
	if (cardinal) return cardinal;
};
var _ordinal = function(n, parse, milparse, multiple) {
	var dec =  decs(n), ordinal, o;
	if (n > 0 || n === '') {
		ordinal = parse(ordinals[n]);

		if (n > 10) {
			if (!ordinal)
				if (n < 20)
					// e.g. tertius decimus
					ordinal = combine(parse(ordinals[dec[0]]), ' ', parse(ordinals[dec[1]*10]));
				else if (n < 100)
					// e.g. vicensimus primus
					ordinal = combine(parse(ordinals[dec[1]*10]), ' ', parse(ordinals[dec[0]]));
				else if (n < 1000)
					ordinal = combine(_cardinal(n%100, parse), ' et ', parse(ordinals[dec[2]*100]));
				else {
					var et = '', mult = '';
					if (dec[0] || dec[1] || dec[2])
						et = combine(dec[2]?parse(cardinals[dec[2]*100]):'', ' ', dec[0]||dec[1]?_cardinal(n%100, parse):'', ' et ');

					if (n >= 2000)
						mult = _adverbial(Math.floor(n/1000), milparse);
					ordinal = combine(et, mult, ' ', parse(ordinals[1000]));
				}

			if (n < 100 && (n % 10) == 8 && n != 98)
				o = combine('duodē',parse(ordinals[n+2]));
			else if (n < 100 && (n % 10) == 9)
				o = combine('ūndē',parse(ordinals[n+1]));

			if (o && ordinal != o)
				if (ordinal && multiple) ordinal = ordinal + ' / ' + o;
				else ordinal = o;
		}
	}
	if (ordinal) return ordinal;
};
var _distributive = function(n, parse, milparse, multiple) {
	var dec =  decs(n), distributive, d;
	if (n > 0 || n === '') {
		distributive = parse(distributives[n]);

		if (n > 10) {
			if (!distributive)
				if (n < 20)
					// e.g. terni deni
					distributive = combine(parse(distributives[dec[0]]), ' ', parse(distributives[dec[1]*10]));
				else if (n < 100)
					// e.g. viceni singuli
					distributive = combine(parse(distributives[dec[1]*10]),' ',parse(distributives[dec[0]]));
				else if (n < 1000)
					distributive = combine(parse(distributives[dec[2]*100]), ' et ', _distributive(n%100, parse, multiple));
				else {
					var et = '';
					if (dec[0] || dec[1] || dec[2])
						et = combine(' et ', dec[2]?parse(distributives[dec[2]*100]):'', ' ', dec[0]||dec[1]?_distributive(n%100, parse):'');

					if (n >= 2000)
						distributive = combine(_distributive(Math.floor(n/1000), milparse), ' ', parse(milia), et, ' (+GEN)');
					else distributive = combine(parse(distributives[1000]), et);
				}

			if (n < 100 && (n % 10) == 8 && n != 98)
				d = combine('duodē',parse(distributives[n+2]));
			else if (n < 100 && (n % 10) == 9)
				d = combine('ūndē',parse(distributives[n+1]));

			if (d && distributive != d)
				if (distributive && multiple) distributive = distributive + ' / ' + d;
				else distributive = d;
		}
	}
	if (distributive) return distributive;
};
var _adverbial = function(n, parse, milparse, multiple) {
	var dec =  decs(n), adverbial, a;
	if (n > 0 || n === '') {
		adverbial = parse(adverbials[n]);

		if (n > 10) {
			if (!adverbial)
				if (n < 20)
					adverbial = combine(parse(adverbials[dec[0]]), ' ', parse(adverbials[dec[1]*10]));
				else if (n < 100)
					adverbial = combine(parse(adverbials[dec[1]*10]), ' ', parse(adverbials[dec[0]]));
				else if (n < 1000)
					adverbial = combine(_cardinal(n%100, parse), ' et ', parse(adverbials[dec[2]*100]));
				else {
					var et = '', mult = '';
					if (dec[0] || dec[1] || dec[2])
						et = combine(' et ', dec[2]?parse(adverbials[dec[2]*100]):'', ' ', dec[0]||dec[1]?_adverbial(n%100, parse):'');

					if (n >= 2000)
						mult = _adverbial(Math.floor(n/1000), milparse);
					adverbial = combine(mult, ' ', parse(adverbials[1000]), et);
				}

			if (n < 100 && (n % 10) == 8 && n != 98)
				a = combine('duodē',parse(adverbials[n+2]));
			else if (n < 100 && (n % 10) == 9)
				a = combine('ūndē',parse(adverbials[n+1]));

			if (a && adverbial != a)
				if (adverbial && multiple) adverbial = adverbial + ' / ' + a;
				else adverbial = a;
		}
	}
	if (adverbial) return adverbial;
};

module.exports.getcardinal     = getter(_cardinal);
module.exports.getordinal      = getter(_ordinal);
module.exports.getdistributive = getter(_distributive);
module.exports.getadverbial    = getter(_adverbial);

module.exports.verbalize = function({number: n, _number, _case, _gender}) {
	var cardinal, ordinal, distributive, adverbial;
	if (n !== '') n = +n;
	var parse = function(verb) {
		if (typeof verb === 'object' && _number in verb)
			verb = verb[_number];
		if (typeof verb === 'object' && _case  in verb)
			verb = verb[_case];
		if (typeof verb === 'object' && _gender in verb)
			verb = verb[_gender];
		if (typeof verb === 'object')
			verb = null;
		return verb;
	};
	var mparse = milparse(_case);
	var m = false;
	ordinal      =      _ordinal(n, parse, mparse, m);
	cardinal     =     _cardinal(n, parse, mparse, m);
	distributive = _distributive(n, parse, mparse, m);
	adverbial    =    _adverbial(n, parse, mparse, m);
	if (!cardinal)
		cardinal = '';
	else
		cardinal = cardinal.split('(n)').join('n');
	if (!ordinal)
		ordinal = '';
	else
		ordinal = ordinal.split('(n)').join('n');
	if (!distributive)
		distributive = '';
	else
		distributive = distributive.split('(n)').join('n');
	if (!adverbial)
		adverbial = '';
	else
		adverbial = adverbial.split('(n)').join('n');
	return {cardinal, ordinal, distributive, adverbial};
};
