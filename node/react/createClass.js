var React = require('react');
var h = require('react-hyperscript');

module.exports = function createClass(c) {
	var r = React.createClass(c);
	r.h = h.bind(undefined, r);
	return r;
};
