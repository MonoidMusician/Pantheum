var h = require('react-hyperscript');

var EditableText = require('../../components/EditableText');

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
	render: function renderDefinitions() {
		var edit;
		var user = this.props.user || this.context.user;
		if (user && user.administrator)
			edit = view.Icon.h({type:"delete"});
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
