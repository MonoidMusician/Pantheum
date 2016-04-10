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
	for (let M of [model.Definition, model.Word]) {
		li.push('<li><a href="/api/'+M.table+'">'+M.table);
		router.route('/'+M.table)
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
			res.json(results);
		}));

		router.route('/'+M.table+'/:id')
		.get(handler(function*(req, res) {
			var id = +req.params.id;
			var m = M({id}, false);
			yield m.pull();
			res.json(m.toJSON());
		}))
		.delete(handler(function*(req, res) {
			var id = +req.params.id;
			var m = M({id}, false);
			if ((yield m.delete()) === null)
				throw new Error(M.table+'/'+id+' did not exist');
			res.send('success');
		}));

		for (let c of M.columns) {
			router.route('/'+M.table+'/:id/'+c)
			.get(handler(function*(req, res) {
				var id = +req.params.id;
				var m = M({id}, false);
				yield m.pull();
				res.send(m[c]);
			}));
		}

		if (M.references)
		for (let R of M.references) {
			router.route('/'+M.table+'/:id/'+R.table)
			.get(handler(function*(req, res) {
				var id = +req.params.id;
				var m = M({id}, false);
				yield m.pullchildrenscarce([R]);
				var rs = m[R.table];
				res.json(rs.map(r=>r.toJSON()));
			}));
			router.route('/'+M.table+'/:id/'+R.table+'/:n')
			.get(handler(function*(req, res) {
				var id = +req.params.id;
				var n  = +req.params.n;
				var m = M({id}, false);
				yield m.pullchildrenscarce([R]);
				var rs = m[R.table];
				if (!(n in rs))
					throw new Error('Index n='+n+' into the '+R.table+' of word '+M.table+'/'+id+' out of bounds (length: '+rs.length+')');
				res.redirect('/api/'+R.table+'/'+rs[n].id);
			}));
		}

		if (M.reference) {
			router.route('/'+M.table+'/:id/'+M.reference)
			.get(handler(function*(req, res) {
				var id = +req.params.id;
				var m = M({id}, false);
				yield m.pull();
				var r = m[M.reference];
				res.redirect('/api/'+r.table+'/'+r.id);
			}));
		}

		if (M.route) M.route(router);
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
