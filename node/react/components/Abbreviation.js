var h = require('react-hyperscript');

var createClass = require('../createClass');
var $dom = require('../jquery-dom');

module.exports = createClass({
	displayName: 'view.Abbreviation',
	render: function renderAbbreviation() {
		return h('abbr', {title:this.props.title}, this.props.children);
	},
	componentDidMount: function() {
		$dom(this).qtip({
			style: {
				classes: "qtip-light qtip-abbr"
			},
			position: {
				at: "top center",
				my: "bottom center",
				adjust: {y:5},
			},
			show: {
				delay: 200,
			},
			hide: {
				fixed: true,
				delay: 100,
			}
		});
	},
	componentDidUpdate: function() {
		// Hint qtip to update
		$dom(this).attr('title', this.props.title);
	},
	componentWillUnmount: function() {
		$dom(this).qtip('destroy', true);
	}
});
