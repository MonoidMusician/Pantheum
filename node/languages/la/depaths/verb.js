var model = require('../../../model');

var parts = {
	"number": ["singular","plural"],
	"case":["vocative","nominative","accusative","ablative","dative","genitive","locative"],
	"gender":["feminine","masculine","neuter"],
	"person":["person-1","person-2","person-3"],
	"voice":["active","passive"],
	"tense":["present","imperfect","future","perfect","pluperfect","future-perfect"]
};
module.exports = new model.Depath('la/verb', {"mood":{
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
}});

// TODO: should be module
function _in_ignore(p,ignore) {
	if (!ignore) return false;
	var base = model.Path(p);
	return ignore.some(ig => p.issub(base.reset().add2(ig), true));
}

function _filter_ignore(values, ignore, p, empty=true, prev=null) {
	var ret = [];
	if (values !== null && values !== false) {
		if (prev) {
			if (!prev[0]) prev[0]=[false];
			var count = [];
			ret['_'] = [];
			for (var k of prev[0]) {
				ret[k] = _filter_ignore(values, ignore, model.Path(p,k), empty, array_slice(prev, 1));
				// TODO: only works when prev.length === 1
				count.push(...ret[k]);
			}
			for (let v of values) {
				if (count.includes(v))
					ret['_'].push(v);
			}
		} else {
			for (var v of values) {
				if (!v || !_in_ignore(model.Path(p,v),ignore))
					ret.push(v);
			}
		}
	}
	if (empty || ret)
		return ret;
	return [false];
}

function table_values(word, ignore) {
	var values = [];
	values.length = 5;
	var moods = values[0] = model.Path({word}).iterate("mood");
	values[1] = [];
	values[2] = [];
	values[3] = [];
	values[4] = [];

	var hspan4 = [];
	var persons = model.Path({word},"indicative").iterate("person");
	var persons = _filter_ignore(persons, ignore, model.Path({word},"indicative"));
	for (var _ of persons) hspan4.push(false);

	var hacked = null;

	for (let _0 of moods) {
		if (ignore !== null && ignore.includes(_0))
			continue;
		values[_0] = [values[0]];
		values[_0].length = values.length;
		var path = model.Path({word},_0);
		switch (_0) {
			case 'indicative':
			case 'subjunctive':
			case 'imperative':
				values[_0][1] = path.iterate("voice");
				values[_0][2] = path.iterate("tense");
				values[_0][3] = path.iterate("number");
				values[_0][4] = model.Path({word},"indicative").iterate("person");
				if (!values[1]) values[1] = [false];
				break;
			case 'participle':
				//values[_0][1] = [""];
				values[_0][2] = path.iterate("tense");
				values[_0][3] = path.iterate("voice");
				values[_0][4] = hspan4;
				break;
			case 'infinitive':
				//values[_0][1] = [""];
				values[_0][2] = path.iterate("tense");
				values[_0][3] = path.iterate("voice");
				values[_0][4] = hspan4;
				break;
			case 'supine':
			case 'gerund':
				let i = values[0].indexOf(_0);
				if (hacked !== null) {
					if (i > -1) values[0].splice(i,1);
					values[_0][3][hacked].push(_0);
					continue;
				}
				var hacked = "";
				if (i > -1) values[0][i] = hacked;

				values[_0][1] = [""];
				values[_0][2] = model.Path({word},"gerund").iterate("case");
				if (!values[_0][2])
					values[_0][2] = model.Path({word}, "supine").iterate("case");
				values[_0][3] = [_0];
				values[_0][4] = hspan4;

				_0 = hacked;
				break;
		}
	}
	return values;
};

module.exports.table_values = table_values;
