var h = require('react-hyperscript');

var createClass = require('../createClass');
var $dom = require('../jquery-dom');

var languages = {
	"la": "Latin",
	"en": "English",
};

module.exports = createClass({
	displayName: 'view.Language',
	render: function renderLanguage() {
		var title = this.props.name || languages[this.props.children];
		return h('sup', {title}, ["[",this.props.children.toString(),"]"]);
	},
	componentDidMount: function() {
		$dom(this).qtip({
			style: {
				classes: "qtip-light qtip-abbr"
			},
			position: {
				at: "top center",
				my: "bottom left",
				adjust: {y:5},
			},
			show: {
				delay: 1000,
			},
			hide: {
				fixed: true,
				delay: 100,
			}
		});
	}
});
