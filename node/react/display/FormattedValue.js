var h = require('react-hyperscript');

var createClass = require('../createClass');

module.exports = createClass({
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
