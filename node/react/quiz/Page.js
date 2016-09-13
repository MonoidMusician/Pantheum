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
			view: this.props.view||false,
			state: this.props.state || (this.props.data ? "loaded" : "loading"),
			data: this.props.data||[],
		};
	},
	toggle() {
		if (this.state.view !== null)
			this.setState({view: !this.state.view});
	},
	render: function renderApp() {
		var actions;
		if (false&&this.state.view) switch (this.state.state) {
			case "loading":
				actions = h(MaterialUI.CircularProgress);
				break;
			case "loaded":
				actions = h(MaterialUI.FlatButton, {
					label: "Submit",
					primary: true,
					style: {margin:'12px 0'},
				});
				break;
			case "scored":
				actions = h(MaterialUI.FlatButton, {
					label: "Next",
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
				style: {
					display: this.state.view ? 'block' : 'none',
				},
			}, this.state.data.map(q => React.isValidElement(q) ? q : Question.h(q))),
			actions,
		]);
	},
});
