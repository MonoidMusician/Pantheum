var connection = require('./mysql');
var model = require('./model');
var ty = require('then-yield');


var parts = {
	"number": ["singular","plural"],
	"case":["nominative","accusative","ablative","dative","genitive"],
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
		console.log(W.definitions.map(_=>_.toData()));
		console.log(W.definitions[1].tag, ''+W.definitions[1].tag);
	} catch(e) {
		err = e;
		console.log(e.stack);
	}
	connection.end();
	if (e) throw e;
});
