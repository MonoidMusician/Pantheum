var React = require('react');
// FIXME: don't require both!
var ReactDOM = require('react-dom');
var ReactDOMServer = require('react-dom/server');
var h = require('react-hyperscript');

var view = {React, ReactDOM, ReactDOMServer, h};
module.exports = view;

var model = require('../model/model');


view.createClass = function createClass(c) {
	var r = React.createClass(c);
	r.h = h.bind(undefined, r);
	return r;
};
view.$dom = function $dom(component) {
	return $(ReactDOM.findDOMNode(component));
};

view.expand = require('./expand');


[
	'view',
	'page',
	'format',
	'inflection-table',
	'attributes',
	'dictionary',
].forEach(f => require('./'+f)(view));
