var React = require('react');

var createClass = require('../createClass');

var EditableText = require('../components/EditableText');

module.exports = createClass({
	displayName: 'view.WordName',
	handleNewValue(name) {
		console.log(name);
	},
	contextTypes: {
		user: React.PropTypes.object,
	},
	render: function renderWordName() {
		var classes = ["word-name"];
		var user = this.props.user || this.context.user;
		if (this.props.word.lang)
			classes.push("format-word-"+this.props.word.lang);
		return EditableText.h({
			disabled: !user || !user.administrator,
			spanClassName: classes.join(" "),
			onNewValue: this.handleNewValue,
			value: this.props.word.name,
			display: this.props.word.entry,
		});
	}
});
