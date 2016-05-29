var h = require('react-hyperscript');

var createClass = require('../createClass');
var $dom = require('../jquery-dom');
var la_ipa = require('../../lib/la_ipa');

var span = createClass({
	displayName: 'view.span',
	render: function renderspan() {
		return h('span', this.props, this.props.children);
	},
	componentDidMount: function() {
		$dom(this).qtip({
			style: {
				classes: "qtip-light"
			},
			position: {
				at: "top center",
				my: "bottom center",
				adjust: {y:5},
			},
			show: {
				delay: 400,
			},
			hide: {
				fixed: true,
				delay: 100,
			},
			content: {
				text: this.props.tooltip,
			},
		});
	}
});

module.exports = createClass({
	displayName: 'view.FormattedWord',
	render: function renderFormattedWord() {
		var {value:v, tooltip} = this.props;
		if (typeof v === 'object' && 'value' in v) {
			let orig = v;
			if (!tooltip) tooltip = function default_tooltip() {
				var text = la2en(orig, true);
				return text[0].toUpperCase() + text.substr(1);
			};
			v = v.value;
		}
		if (v) v = la_ipa.transform(v);
		else v = '\u2014';
		return span.h({tooltip}, v);
	},
});
