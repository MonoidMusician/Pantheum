var connection = require('./mysql');
var model = require('./model');
var ty = require('then-yield');


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

connection.connect();
ty.spawn(function*() {
	var err;
	try {
		var W = model.Word({id: 10176}, true);
		console.log(W);
		yield W.pull();
		console.log(W.toJSON());
		yield W.pullchildren();
		var tag = W.definitions[1].tag;
		var data = tag.toData();
		delete data.word;
		console.log(''+tag, data);
		console.log(W.path('future/active/participle'));
	} catch(e) {
		err = e;
		console.log(e.stack);
	}
	connection.end();
	if (err) throw err;
});
