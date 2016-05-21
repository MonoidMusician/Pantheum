var h = require('react-hyperscript');
var la_ipa = require('../la_ipa');

module.exports = function(view) {
	view.format_value = function format_value(value) {
		return view.FormattedValue.h({value});
	};
	view.FormattedValue = view.createClass({
		displayName: 'view.FormattedValue',
		render: function renderFormattedValue() {
			var {value:v} = this.props;
			v = v.split('///')[0];
			var o = {
				'person-1': '1st person',
				'person-2': '2nd person',
				'person-3': '3rd person',
			};
			if (v in o) v = o[v];
			else v = v[0].toUpperCase()+v.substr(1).split('-').join(' ');
			return h('span', v);
		},
	});
	view.format_word = function format_word(value) {
		return view.FormattedWord.h({value});
	};

	view.span = view.createClass({
		displayName: 'view.span',
		render: function renderspan() {
			return h('span', this.props, this.props.children);
		},
		componentDidMount: function() {
			view.$dom(this).qtip({
				style: {
					classes: "qtip-light"
				},
				position: {
					at: "top center",
					my: "bottom center",
					adjust: {y:5},
				},
				show: {
					delay: 600,
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

	view.FormattedWord = view.createClass({
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
			return view.span.h({tooltip}, v);
		},
	});
};


