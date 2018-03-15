var model = require('../model');
var user = require('../user');
var {Combo, PermuteOrder} = require('../lib/combo');

var parts = {
	"number": ["singular","plural"],
	"case":["vocative","nominative","accusative","ablative","dative","genitive","locative"],
	"gender":["feminine","masculine","neuter"],
	"person":["person-1","person-2","person-3"],
	"voice":["active","passive"],
	"tense":["present","imperfect","future","perfect","pluperfect","future-perfect"]
};
model.Depath.add("la", "verb", new model.Depath("verb", {"mood":{
	"indicative":{
		"voice": parts["voice"],
		"tense": parts["tense"],
		"number": parts["number"],
		"person": parts["person"],
		"gender": parts["gender"]
	},
	"subjunctive":{
		"voice": parts["voice"],
		"tense":[
			"present",
			"imperfect",
			"perfect",
			"pluperfect"
		],
		"number": parts["number"],
		"person": parts["person"],
		"gender": parts["gender"]
	},
	"imperative":{
		"voice": parts["voice"],
		"tense":[
			"present",
			"future"
		],
		"number": parts["number"],
		"person": parts["person"],
		"gender": parts["gender"]
	},
	"participle":{
		"voice": parts["voice"],
		"tense":[
			"present",
			"perfect",
			"future"
		],
		"number": parts["number"],
		"gender": parts["gender"],
		"case": parts["case"]
	},
	"infinitive":{
		"voice": parts["voice"],
		"tense":[
			"present",
			"perfect",
			"future"
		],
		"number": parts["number"],
		"gender": parts["gender"],
		"case":[
			"nominative",
			"accusative"
		]
	},
	"gerund":{
		"case":[
			"accusative",
			"ablative",
			"dative",
			"genitive"
		]
	},
	"supine":{
		"case":[
			"accusative",
			"ablative"
		]
	}
}}));

var translate = {};
translate["en"] = function la2en(path, only_one=false) {
	var inflect = en.inflect;
	var APOS = '’'; // FIXME
	var arch = user.udata && user.data.archtrans;

	var decide = function(archaïc, standard, extra) {
		if (only_one) return arch ? archaïc : standard;
		return '('+[archaïc,standard,extra].filter(Boolean).join('|')+')';
	};

	var {word, spart} = word;
	var definitions = get_definitions(path);

	var verb = spart === 'verb', noun = spart === 'noun';
	if (verb) {
		var {mood, tense, voice, person, number} = path;

		var psv = voice === 'passive';
		var _p = person[person.length-1];
		var pl = number === 'singular' ? 0 : 1;
	}

	var d0 = [], d1 = [], d2 = [], d3 = [], d4 = [], d5 = [], be = [];

	for (let def of definitions) {
		if (only_one && d0.length) break;
		let match = def.match(/^be(?:\s+(.*)|)$/);
		if (match) {
			if (only_one && be.length) continue;
			be.push(match[1] || ' ');
			continue;
		}
		match = def.match(/^([a-zA-Z-]+)((?:[^a-zA-Z-].*)?)$/);
		if (match) {
			let [a,b] = match.slice(1);
			d0.push(def);
			if (noun) d1.push(inflect.pluralize(def));
			if (!verb) continue;
			d1.push(inflect.preterite(def, only_one));
			d2.push(inflect.pastparticiple(def, only_one));
			d3.push(inflect.presentparticiple(def, only_one));
			d4.push(
				pl || _p == 1 ? def
				: [inflect.secondsingular,inflect.thirdsingular][_p-2](def, only_one)
			);
			d5.push(inflect.secondsingular(inflect.preterite(def, only_one), only_one));
		}
	}
	if (only_one) {
		if (d0.length) d0 = d0[0];
		if (d1.length) d1 = d1[0];
		if (d2.length) d2 = d2[0];
		if (d3.length) d3 = d3[0];
		if (d4.length) d4 = d4[0];
		if (d5.length) d5 = d5[0];
	}

	var d = d0, D = d3;
	if (verb) {
		let t, v, p, b, m;
		let modals, type = 0;
		let subj;

		switch (mood) {
			case 'infinitive':
				let BE = 'be';
				if (tense === 'future')
					modals = new Combo('be', ['about','going'], 'to');
				else if (tense === 'perfect') {
					modals = 'have';
					d = d2;
					BE = 'been';
				}
				if (voice === 'passive') {
					v = tense === 'perfect' ? 'been' : 'be';
					d = d2;
					BE = 'been';
				}

				if (be && !psv) {
					let BE = new Combo(BE, ' ', be);
					if (!d || !d.length) d = BE;
					else d = new Combo([d, BE]);
				}

				return new Combo('to', modals, v, d);

			case 'participle':
				let BE = '';
				d = d3;
				if (tense === 'future') {
					modals = new Combo(['about','going'], 'to');
					d = d0;
					BE = 'be';
				} else if (tense === 'perfect') {
					modals = 'having';
					d = d1;
					BE = 'have been';
				}
				if (voice === 'passive') {
					v = tense === 'perfect' ? 'been' : 'be';
					d = d2;
					BE = 'been';
				}

				if (be && !psv) {
					BE = new Combo(BE, ' ', be);
					if (!d || !d.length) d = BE;
					else d = new Combo([d, BE]);
				}

				return new Combo(modals, v, d);

			case 'subjunctive':
				subj = true;
			case 'indicative':
				let pronoun = [
					'I', 'we',
					new Combo(['you','thou'], ['(sg.)','']),
					new Combo(['','all'], ['you','ye','y'+APOS+'all'], ['(pl.)','']),
					new Combo(['he', 'she', 'it']),
					'they'
				];
				pronoun = _p && pronoun[2*_p+pl-2];

				let is = [
					new Combo('am', APOS+'m'),
					new Combo([
						'are', // standard
						subj && 'beest', 'art', // archaïc
						APOS+'re', APOS+'rt', // abbreviated
					]),
					new Combo([
						'is', // standard
						subj && 'beeth', // archaïc
						APOS+'s', // abbreviated
					]),
				];
				if (pl) is = new Combo(['are', APOS+'re']);
				else is = is[_p-1];
				if (subj) is = new Combo([is, 'be']);

				let was;
				if (pl) was = 'were';
				else if (_p == 2) was = new Combo(['were', subj && 'wert', 'wast']);
				else was = new Combo(['was', subj && 'were']);

				let will = new Combo(['will', APOS+'ll', ['is', ['going','about'], 'to']]);
				if (p == 1) will = new Combo(['shall', will]);
				else if (p == 2 && !pl) will = new Combo(['wilt', 'shall', will]);

				let has = 'have';
				if (_p == 3 && !pl) has = new Combo(['hath', 'has']);
				else if (_p == 2 && !pl) will = new Combo(['hast', 'have', 'havest']);

				if (psv) d = D = d2;

				let copulative;
				let BE = psv ? 'been' : is;

				switch (tense) {
					case 'present':
						copulative = is;
						if (!psv) modals = ' ';
						else {
							modals = is;
							copulative = new Combo(is, 'being');
						}
						if (!psv && _p > 1 && !pl) {
							BE = is;
							if (p == 3) d = d4;
							else d = new Combo(d4, d);
						}
						break;
					case 'imperfect':
						copulative = was;
						if (psv) {
							modals = was;
							copulative = new Combo(was, 'being');
						}
						break;
					case 'future':
						modals = will;
						if (psv) modals = new Combo(will, ' be');
						else copulative = new Combo(will, ' be');
						break;
					case 'perfect':
						d = d2; D = d2;
						BE = 'been';
						if (psv) {
							copulative = was;
							modals = new Combo(has, ' been');
						} else {
							copulative = has;
							modals = ' ';
							d = d1;
							BE = new Combo(has, ' been');
							if (_p == 2 && !pl) {
								d = decide(d5,d1);
							}
						}
						break;
					case 'pluperfect':
						d = d2; D = d2;
						BE = 'been';
						modals = new Combo(decide('hadst','had'), psv?' been':'');
						break;
					case 'future-perfect':
						d = d2; D = d2;
						BE = 'been';
						modals = new Combo(will, 'have', psv?' been':'');
						break;
				}

				if (be && !psv) {
					if (!D) D = BE;
					else D = new Combo([D, BE]);
				}

				if (!d && !D) return d;
				if (!D) copulative = undefined;
				if (!d) modals = undefined;
				if (copulative)
					if (!modals)
						return new Combo(pronoun, copulative, D);
					else
						return new Combo(pronoun, [[modals, d],[copulative, D]]);
				else if (d) return new Combo(pronoun, modals, d);
				return null;
		}
	} else if (noun) {
		if (path.number === 'plural') d = d1;
		let article = new Combo(['a', 'an', 'the', 'some']);
		let preposition;
		switch (path.case) {
			case 'ablative':
				preposition = new Combo(['by', 'from', '']);
			case 'dative':
				preposition = new Combo(['to', 'for']);
			case 'genitive':
				preposition = 'of';
			case 'locative':
				preposition = 'at';
		}
		return new Combo(preposition, article, d);
	} else if (['adjective','adverb'].includes(spart)) {
		let degree;
		switch (path.degree) {
			case 'comparative':
				preposition = new Combo(['quite', 'more', 'rather']);
			case 'superlative':
				preposition = new Combo(['very', 'most']);
		}
		return new Combo(preposition, d);
	}
};


