var h = require('react-hyperscript');

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
	view.FormattedWord = view.createClass({
		displayName: 'view.FormattedWord',
		render: function renderFormattedWord() {
			var {value:v} = this.props;
			//console.log('before:',v);
			if (typeof v === 'object' && 'value' in v) v = v.value;
			if (v) v = la_ipa.transform(v);
			else v = '\u2014';
			//console.log('after:',v);
			return h('span', v);
		},
	});
};
