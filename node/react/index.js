var React = require('react');
// FIXME: don't require both!
var ReactDOM = require('react-dom');
var ReactDOMServer = require('react-dom/server');
var h = require('react-hyperscript');

var view = {React, ReactDOM, ReactDOMServer, h};
module.exports = view;

var model = require('../model');


view.createClass = function createClass(c) {
	var r = React.createClass(c);
	r.h = h.bind(undefined, r);
	return r;
};
view.$dom = function $dom(component) {
	return $(ReactDOM.findDOMNode(component));
};

view.expand = require('./expand');

require('./view')(view);
require('./page')(view);
require('./format')(view);
require('./inflection-table')(view);
require('./attributes')(view);
require('./dictionary')(view);
