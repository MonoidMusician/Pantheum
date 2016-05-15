var express    = require('express');
var url        = require('url');
var Promise    = require('bluebird');
var connection = require('./mysql');
var queryP     = require('./mysqlpromise');
var model      = require('./');
require('../languages/la');

var router = express.Router();
module.exports = router;

connection.connect();

var handle = function(res, err) {
	console.log(err.stack);
	if (err instanceof Error && !Object.keys(err).length)
		err = err.message;
	res.send(err);
};

var handler = function(gen) {
	return Promise.coroutine(function*(req, res, next) {
		try {
			return yield* gen(req, res, next);
		} catch(err) {
			return handle(res, err);
		}
	});
};

model.Word.route = (tablerouter, modelrouter) => modelrouter.route('/path/*').get(handler(function*(req, res, next) {
	"use strict";
	var word = req.model, tag = req.params[0];
	yield word.pullall();
	var p = model.Path({word,tag});
	if (!p.hasvalue()) {
		var r = {};
		var i = word.forms.filter(f=>f.issub(p)).map(f=>r[f.tag]=f.value);
		if (i && i.length)
			return res.json(r);
	}
	return res.send(p.value);
}));
model.Definition.route = (tablerouter, modelrouter) => modelrouter.route('/form_tag').get(handler(function*(req, res, next) {
	yield req.model.pullall();
	res.send(req.model.form_tag);
}));

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
				var inserted = yield Promise.all(req.body.map(d => M.fromJSON.cacheable(true)(d).insert()));
				res.json(inserted.map(m => m.id));
			} else {
				var m = M.fromJSON.cacheable(true)(req.body);
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

		tablerouter.route('/_do/:operation')
		.post(handler(function*(req, res) {
			var op = req.params.operation;
			var data = req.body.data;
			console.log(op, data);
			var arg = req.body.arg || [];
			var m = M.fromJSON.cacheable(true)(data);
			res.json(yield m[op](...arg));
		}));

		// Create subrouter for an instance of this model
		let modelrouter = express.Router();
		tablerouter.use('/:id(\\d+)', [
			// Create model object M({id})
			function(req, res, next) {
				var id = +req.params.id;
				if (Number.isNaN(id)) throw new Error('invalid id '+req.params.id);
				req.model = M({id}, true);
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

		if (M.route) M.route(tablerouter, modelrouter);
	}
})(model));

router.get('/', function(req, res) {
	res.send('<ul>'+li.join('')+'</ul>');
});
