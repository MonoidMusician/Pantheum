// BASE SETUP
// =============================================================================

// call the packages we need
var express    = require('express');
var bodyParser = require('body-parser');
var app        = express();
var morgan     = require('morgan');
var ty         = require('then-yield');
var connection = require('./model/mysql.js');
var queryP     = require('./model/mysqlpromise.js');
var model      = require('./model/model.js');
connection.connect();

// configure app
//app.use(morgan('dev')); // log requests to the console

// configure body parser
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());

var port     = process.env.PORT || 8080; // set our port

// ROUTES FOR OUR API
// =============================================================================

// create our router
var router = express.Router();

// test route to make sure everything is working (accessed at GET http://localhost:8080/api)
var handle = function(res, err) {
	console.log(err.stack);
	if (err instanceof Error && !Object.keys(err).length)
		err = err.message;
	res.send(err);
};
var handler = function(gen) {
	return ty.async(function*(req, res) {
		try {
			return yield* gen(req, res);
		} catch(err) {
			return handle(res, err);
		}
	});
};
var li = [];
((function(model) {
	"use strict";
	function hasflag(req, flag) {
		if (!(flag in req.query)) return false;
		var v = req.query[flag];
		return (v || v === '');
	}
	router.use(function(req, res, next) {
		req.queryUrl = url.parse(req.url).query;
		next();
	});
	for (let M of [model.Definition, model.Form, model.Word]) {
		li.push('<li><a href="/api/'+M.table+'">'+M.table);

		// Create subrouter for this table/model
		let tablerouter = express.Router();
		router.use('/'+M.table, tablerouter);

		tablerouter.route('/')
		.post(handler(function*(req, res) {
			if (Array.isArray(req.body)) {
				var inserted = yield Promise.all(req.body.map(d => M().fromData(d).insert()));
				res.json(inserted.map(m => m.id));
			} else {
				var m = M().fromData(req.body);
				yield m.insert();
				res.json(m.id);
			}
		}))
		.get(handler(function*(req, res) {
			var results = yield queryP('SELECT ?? AS id FROM ??', [M.key, M.table]);
			if (hasflag(req, 'html')) {
				var query = req.queryUrl;
				res.send(results.map(r=>'<a href="/api/'+M.table+'/'+r.id+'?'+query+'">'+r.id+'</a>').join(', '));
			} else res.json(results);
		}));

		// Create subrouter for an instance of this model
		let modelrouter = express.Router();
		tablerouter.use('/:id', [
			// Create model object M({id})
			function(req, res, next) {
				var id = +req.params.id;
				if (Number.isNaN(id)) throw new Error('invalid id '+req.params.id);
				req.model = M({id}, false);
				req.modelUrl = M.table+'/'+id;
				next();
			},
			// Finish acting on it
			modelrouter
		]);
		modelrouter.route('/')
		.get(handler(function*(req, res) {
			var m = req.model, id = m.id;
			var json;
			yield hasflag(req, 'full') ? m.pullall() : m.pull();
			json = m.toJSON();
			if (hasflag(req, 'html')) {
				var query = req.queryUrl;
				var html = '<dl><dt>id</dt><dd>'+id+'</dd>';
				for (let k in json) {
					if (k === 'id') continue;
					html += '<dt><a href="/api/'+M.table+'/'+id+'/'+k+'?'+query+'">'+k+'</a></dt>';
					html += '<dd>'+JSON.stringify(json[k])+'</dd>';
				}
				html += '</dl>';
				if (M.references)
					html += M.references.map(({table:r}) => '<a href="/api/'+M.table+'/'+id+'/'+r+'?'+query+'">'+r+'</a>').join(', ');
				res.send(html);
			} else res.json(json);
		}))
		.delete(handler(function*(req, res) {
			if ((yield req.model.delete()) === null)
				throw new Error(req.modelUrl+' did not exist');
			res.send('success');
		}));

		for (let c of M.columns) {
			modelrouter.route('/'+c)
			.get(handler(function*(req, res) {
				var m = req.model;
				yield m.pull();
				res.send(m[c]);
			}));
		}

		if (M.references)
		for (let R of M.references) {
			modelrouter.route('/'+R.table)
			.get(handler(function*(req, res) {
				var m = req.model;
				if (hasflag(req, 'full'))
					yield m.pullchildren([R]);
				else
					yield m.pullchildrenscarce([R]);
				var results = m[R.table].map(r=>r.toJSON());
				if (hasflag(req, 'html')) {
					var query = req.queryUrl;
					res.send(results.map(r=>'<a href="/api/'+R.table+'/'+r.id+'?'+query+'">'+r.id+'</a>').join(', '));
				} else res.json(results);
			}));
			modelrouter.route('/'+R.table+'/:n')
			.get(handler(function*(req, res) {
				var m = req.model, id = m.id;
				var n = +req.params.n;
				yield m.pullchildrenscarce([R]);
				var rs = m[R.table];
				if (!(n in rs))
					throw new Error('Index n='+n+' of the '+R.table+' of word '+req.modelUrl+' out of bounds (length: '+rs.length+')');
				var query = req.queryUrl; query = query?'?'+query:'';
				res.redirect('/api/'+R.table+'/'+rs[n].id+query);
			}));
		}

		if (M.reference) {
			modelrouter.route('/'+M.reference)
			.get(handler(function*(req, res) {
				var m = req.model;
				yield m.pullall();
				var r = m[M.reference];
				var query = req.queryUrl; query = query?'?'+query:'';
				res.redirect('/api/'+r.table+'/'+r.id+query);
			}));
		}

		if (M.route) M.route(tablerouter);
	}
})(model));

router.get('/', function(req, res) {
	res.send('<ul>'+li.join('')+'</ul>');
});

// REGISTER OUR ROUTES -------------------------------
app.use('/api', router);

var proxy = require('express-http-proxy');
var url = require('url');
var qs = require('qs');

// New hostname+path as specified by question:
var apiProxy = proxy('localhost:8080', {
	forwardPath: function (req, res) {
		return url.parse(req.baseUrl).path+'?'+url.parse(req.url).query;
	}
});
app.use('*', apiProxy);

// START THE SERVER
// =============================================================================
app.listen(port);
console.log('Magic happens on port ' + port);



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


