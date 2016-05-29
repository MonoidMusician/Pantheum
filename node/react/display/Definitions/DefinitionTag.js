var h = require('react-hyperscript');

var createClass = require('../../createClass');
var RevealText = require('../../components/RevealText');

module.exports = createClass({
	displayName: 'view.DefinitionTag',
	getInitialState() {
		return {hover:false};
	},
	render: function renderDefinitionTag() {
		var path = this.props.path, {tag, value} = path;
		var children = [];
		if (!path || (!tag && !value)) return h('span');
		if (tag) {
			if (tag.includes('/'))
				tag = RevealText.h({
					long: tag,
					short: tag.split('/')[0] + '/…',
				});
			children.push(tag);
			if (value) children.push(' ');
		}
		if (value) {
			children.push('“'+value+'”');
		}
		return h('span', ['(',...children,') ']);
	},
});
