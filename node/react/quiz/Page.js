var h = require('react-hyperscript');
var MaterialUI = require('material-ui');
MaterialUI.svgicons = require('material-ui/svg-icons');
MaterialUI.styles = require('material-ui/styles');

var createClass = require('../createClass');

var Question = require('./Question');

module.exports = createClass({
	displayName: 'view.Page',
	getInitialState() {
		return {
			status: this.props.status || (this.props.data ? "loaded" : "loading"),
			data: this.props.data||[],
		};
	},
	render: function renderApp() {
		var actions;
		if (false&&this.state.view) switch (this.state.status) {
			case "loading":
				actions = h(MaterialUI.CircularProgress, {key:3});
				break;
			case "loaded":
				actions = h(MaterialUI.FlatButton, {
					label: "Submit",
					primary: true,
					style: {margin:'12px 0'},
					key: 3,
				});
				break;
			case "scored":
				actions = h(MaterialUI.FlatButton, {
					label: "Next",
					key: 3,
				});
		}
		return h('div', [
			//h('a', {key: 0, href:'javascript:void(0)', onClick:this.toggle}, this.props.name),
			this.props.help && h('div', {
				key: 1,
				style: {
					color: 'orange',
				},
			}, this.props.help),
			h('div', {
				key: 2,
			}, this.state.data.map(q => React.isValidElement(q) ? q : Question.h(q))),
			actions,
		]);
	},
});
