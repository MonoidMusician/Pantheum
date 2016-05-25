var React = require('react');
var h = require('react-hyperscript');

var createClass = require('../createClass');

module.exports = createClass({
	childContextTypes: {
		user: React.PropTypes.object,
	},
	getChildContext: function() {
		return {user: this.props.user};
	},
	render: function() {
		return h(this.props.type || 'div', this.props, this.props.children);
	},
});
