var h = require('react-hyperscript');
var MaterialUI = require('material-ui');
MaterialUI.svgicons = require('material-ui/svg-icons');
MaterialUI.styles = require('material-ui/styles');

var createClass = require('../createClass');

module.exports = createClass({
	displayName: 'view.Page',
	getInitialState() {
		return {
			value: this.props.value||"",
		};
	},
	toggle() {
		if (this.state.view !== null)
			this.setState({view: !this.state.view});
	},
	render: function renderApp() {
		if (this.props.type === 'question-FR') {
			if (this.props.scored) {
				return h('span', {}, [this.state.value, ' (', this.props.answer, ')']);
			}
			return h(MaterialUI.TextField, {
				hintText: this.props.label,
				floatingLabelText: this.props.floating,
				value: this.state.value,
				onChange: (e,value) => this.setState({value}),
				key: this.props.key,
			});
		}
		return h('span', "INVALID QUESTION");
	},
});
