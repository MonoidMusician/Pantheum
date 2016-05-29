var React = require('react');
var h = require('react-hyperscript');

var EditableText = require('../../components/EditableText');
var Icon = require('../../components/Icon');

var createClass = require('../../createClass');
var Language = require('../Language');

var DefinitionTag = require('./DefinitionTag');

module.exports = createClass({
	displayName: 'view.Definitions',
	handleNewValue(id) {
		return (name) => {
			console.log(name);
		};
	},
	contextTypes: {
		user: React.PropTypes.object,
	},
	render: function renderDefinitions() {
		var edit;
		var user = this.props.user || this.context.user;
		if (user && user.administrator)
			edit = Icon.h({type:"delete"});
		return h('ol', this.props.definitions.map((def, key) => {
			return h('li', {key}, [
				Language.h(def.lang),
				DefinitionTag.h({path:def.tag}),
				EditableText.h({
					disabled: !user || !user.administrator,
					onNewValue: this.handleNewValue(def.id),
					value: def.value.split('\n').join(', '),
				}),
				edit
			]);
		}));
	}
});
