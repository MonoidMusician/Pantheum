var user = require('../../user');
var {Combo, PermuteOrder} = require('../../lib/combo');
var inflect = require('../en/inflect');

var translate = {};
module.exports = translate;



function isspread(v) {
	return (Array.isArray(v) && (c=>c||c===undefined)(v[Symbol.isConcatSpreadable]));
}
var flatten = (list) => list.reduce((V, v) => V.concat(isspread(v) ? flatten(v) : v), []);

function split_definitions(defs) {
	if (Array.isArray(defs))
		return flatten(defs.map(split_definitions));
	return defs.split(/[,;\n]+/g).map(a=>a.trim());
}
function cull_definitions(defs) {
	return defs.filter(d=>!d.tag.keylength).map(d=>d.value);
}
function get_definitions(word, path) {
	return split_definitions(cull_definitions(word.definitions, path));
}

function choices(...arg) {
	return new Combo(arg.filter(a=>typeof a !== 'boolean'));
}
function phrase(...arg) {
	var inter = [];
	for (let a of arg)
		if (typeof a !== 'boolean')
			inter.push(a, ' ');
	inter.pop();
	return new Combo(...inter);
}

translate["en"] = function la2en(path, only_one=false) {
	var APOS = '’'; // FIXME
	var arch = user.udata && user.data.archtrans;

	var choose = function(...arg) {
		var [standard, archaïc, extra] = arg.map(
			c => isspread(c) ? choices(...c) : choices(c)
		);
		if (only_one)
			return arch && archaïc && archaïc.length ? archaïc : standard;
		if (!archaïc || !archaïc.data.length) archaïc = false;
		if (!extra || !extra.data.length) extra = false;
		return choices(archaïc, standard, extra);
	};
	var abbr = function(...arg) {
		return arg.map(s=>APOS+s);
	};

	var {word} = path; var {spart} = word;
	var definitions = get_definitions(word, path);
	//console.log(spart, definitions);

	var verb = spart === 'verb', noun = spart === 'noun';
	if (verb) {
		var {mood, tense, voice, person, number} = path.mapping();

		var psv = voice === 'passive';
		var _p = person && person[person.length-1];
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
			if (_p) {
				d4.push(
					pl || _p == 1 ? def
					: [inflect.secondsingular,inflect.thirdsingular][_p-2](def, only_one)
				);
				d5.push(inflect.secondsingular(inflect.preterite(def, only_one), only_one));
			}
		}
	}
	//console.log(d0,d1,d2,d3,d4,d5);
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
		let modals, BE;
		let subj = false;

		switch (mood) {
			case 'infinitive':
				modals = true;
				BE = 'be';
				if (tense === 'future')
					modals = phrase('be', ['about','going'], 'to');
				else if (tense === 'perfect') {
					modals = 'have';
					d = d2;
					BE = 'been';
				}
				if (voice === 'passive') {
					modals = phrase(modals, tense === 'perfect' ? 'been' : 'be');
					d = d2;
					BE = 'been';
				}

				if (be && be.length && !psv) {
					BE = phrase(BE, be);
					if (!d || !d.length) d = BE;
					else d = choices(d, BE);
				}

				return phrase('to', modals, d);

			case 'participle':
				modals = true;
				BE = '';
				d = d3;
				if (tense === 'future') {
					modals = phrase(['about','going'], 'to');
					d = d0;
					BE = 'be';
				} else if (tense === 'perfect') {
					modals = 'having';
					d = d1;
					BE = 'have been';
				}
				if (voice === 'passive') {
					modals = phrase(modals, tense === 'perfect' ? 'been' : 'be');
					d = d2;
					BE = 'been';
				}

				if (be && be.length && !psv) {
					BE = phrase(BE, be);
					if (!d || !d.length) d = BE;
					else d = choices(d, BE);
				}

				return phrase(modals, d);

			case 'subjunctive':
				subj = true;
			case 'indicative':
				let pronoun = [
					'I', 'we',
					'thou',/*/choose(['you (sg.)', 'you'], 'thou'),/*/
					'ye',/*/phrase(['','all'], choose(['you (pl.)', 'you'], 'ye', ['y'+APOS+'all'])),/*/
					'he',/*/choices('he', 'she', 'it'),/**/
					'they'
				];
				pronoun = _p && pronoun[2*_p+pl-2];

				let is = [
					choices('am', APOS+'m'),
					choose([
						'are', // standard
					], [
						subj && 'beest', 'art' // archaïc
					], abbr('re', 'rt')),
					choose([
						'is', // standard
					], [
						subj && 'beeth', // archaïc
					], abbr('s')),
				];
				if (pl) is = choices('are', APOS+'re');
				else is = is[_p-1];
				if (subj) is = choices(is, 'be');

				let was;
				if (pl) was = 'were';
				else if (_p == 2) was = choose('were', [subj && 'wert', 'wast']);
				else was = choose('was', subj && 'were');

				let will = choices('will', APOS+'ll', phrase(is, ['going','about'], 'to'));
				if (_p == 1) will = choices('shall', will);
				else if (_p == 2 && !pl) will = choose('shall', ['wilt', 'shalt'], will);

				let has = 'have';
				if (_p == 3 && !pl) has = choose('has', 'hath');
				else if (_p == 2 && !pl) has = choose('have', ['hast', 'havest']);

				if (psv) d = D = d2;

				let copulative;
				BE = psv ? 'been' : true;

				switch (tense) {
					case 'present':
						copulative = is;
						if (!psv) modals = ' ';
						else {
							modals = is;
							copulative = phrase(is, 'being');
						}
						if (!psv && _p > 1 && !pl) {
							if (_p == 3) d = d4;
							else d = choices(d4, d);
						}
						break;
					case 'imperfect':
						copulative = was;
						if (psv) {
							modals = was;
							copulative = phrase(was, 'being');
						}
						break;
					case 'future':
						modals = will;
						if (psv) modals = phrase(will, 'be');
						else copulative = phrase(will, 'be');
						break;
					case 'perfect':
						d = d2; D = d2;
						BE = 'been';
						if (psv) {
							copulative = was;
							modals = phrase(has, 'been');
						} else {
							copulative = has;
							modals = ' ';
							d = d1;
							//BE = phrase(has, 'been');
							if (_p == 2 && !pl) {
								d = choose(d1,d5);
							}
						}
						break;
					case 'pluperfect':
						d = d2; D = d2;
						BE = 'been';
						let had = 'had';
						if (_p == 2 && !pl) had = choose(had, 'hadst');
						modals = phrase(had, psv&&'been');
						break;
					case 'future-perfect':
						d = d2; D = d2;
						BE = 'been';
						modals = phrase(will, 'have', psv&&'been');
						break;
				}

				//console.log({D,be,BE});
				if (be && be.length && !psv) {
					BE = phrase(BE, be);
					if (!D || !D.length) D = BE;
					else D = choices(D, BE);
				}
				//console.log(D);

				if (!d && !D) return d;
				if (!D) copulative = undefined;
				if (!d) modals = undefined;
				//console.log({ copulative, modals, d, D });
				//console.log([copulative,modals,d,D].map(d=>typeof d === 'string'?d:(d.repr||d.toString).call(d)));
				if (copulative)
					if (!modals)
						return phrase(pronoun, copulative, D);
					else
						return phrase(pronoun, [phrase(modals, d), phrase(copulative, D)]);
				else if (d) return phrase(pronoun, modals, d);
				return null;
		}
	} else if (noun) {
		if (path.key_value('number') === 'plural') d = d1;
		let article = choices('a', 'an', 'the', 'some');
		let preposition = false;
		switch (path.key_value('case')) {
			case 'ablative':
				preposition = choices('by', 'from', '');
			case 'dative':
				preposition = choices('to', 'for');
			case 'genitive':
				preposition = 'of';
			case 'locative':
				preposition = 'at';
		}
		return phrase(preposition, article, d);
	} else if (['adjective','adverb'].includes(spart)) {
		let preposition = false;
		switch (path.key_value('degree')) {
			case 'comparative':
				preposition = choices('quite', 'more', 'rather');
			case 'superlative':
				preposition = choices('very', 'most');
		}
		return phrase(preposition, d);
	}
	return choices(...d);
};
