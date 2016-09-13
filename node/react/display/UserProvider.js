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
		var props = Object.assign({}, this.props);
		delete props.user;
		return h(this.props.type || 'div', props, this.props.children);
	},
});
