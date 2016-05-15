var express        = require('express');
var url            = require('url');
var Promise        = require('bluebird');
var ReactDOMServer = require('react-dom/server');
var fs             = require('fs');
require('./languages/la');

var view = require('./react');
var model = require('./model');

var router = express.Router();
module.exports = router;

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

router.route('/').get(function(req, res) {
	var root = view.Page.h({
		title: 'HALLO',
	}, 'hello');
	res.send(ReactDOMServer.renderToString(root));
});

router.route('/dictionary').get(handler(function*(req, res) {
	yield model.Word({
		id: 10176,
		entry: "sum, esse, fui", // TODO: should be calculated from spart and forms (and attrs)
		attrs: {
			common:true,
			copulative:true,
			irregular:true,
			transitive:false
		}
	}, true).pullall().then(function(word) {
		var entry = view.Entry.h({word});
		var root = view.Page.h({
			title: 'Dictionary',
			links: [
				{rel:'stylesheet', type:'text/css', href:'/CSS/react.css'},
				{rel:'stylesheet', type:'text/css', href:'/Images/open-iconic/font/css/open-iconic.css'},
			]
		}, entry);
		res.send(ReactDOMServer.renderToString(root));
	});
}));

router.route('/playground').get(handler(function*(req, res) {
	var file = fs.readFileSync('../playground.html', 'utf8');
	model.Word({
		id: 10176,
		entry: "sum, esse, fui", // TODO: should be calculated from spart and forms (and attrs)
		attrs: {
			common:true,
			copulative:true,
			irregular:true,
			transitive:false
		}
	}, true).pullall().then(function(word) {
		var entry = view.Entry.h({word});
		var root = view.Page.h({
			title: 'Dictionary',
			links: [
				{rel:'stylesheet', type:'text/css', href:'/CSS/react.css'},
				{rel:'stylesheet', type:'text/css', href:'/Images/open-iconic/font/css/open-iconic.css'},
			]
		}, entry);
		file.replace(
			'<div id="dictionary"></div>',
			ReactDOMServer.renderToString(entry)
		);
		res.send(file);
	});
}));
