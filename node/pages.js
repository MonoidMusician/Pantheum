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

for (let page in view.pages) {
	router.route('/'+page).get(handler(function*(req, res) {
		var data = yield Promise.resolve(view.pages[page].data());
		var html = view.Page.h({
			page, data,
		});
		res.send(ReactDOMServer.renderToStaticMarkup(html));
	}));
}
