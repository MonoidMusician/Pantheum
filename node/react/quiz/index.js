var h = require('react-hyperscript');
var MaterialUI = require('material-ui');
MaterialUI.svgicons = require('material-ui/svg-icons');
MaterialUI.styles = require('material-ui/styles');

var createClass = require('../createClass');

var Page = require('./Page');

var Question = require('./Question');

module.exports = createClass({
	displayName: 'view.Quiz',
	getInitialState() {
		return {
			current_page: 0,
			max: 2,
			pages: [{
				name: "Page 1",
				view: true,
				help: "Type “Hello” as the correct answer!",
				data: [{
					key: 0,
					type: 'question-FR',
					label: "Type hello",
					floating: "Hello",
					value: "hello",
					answer: "hello",
					scored: true,
				}].map(q => Question.h(q)),
				state: "scored",
			}, {
				name: "Page 2",
				view: false,
				data: [{
					key: 1,
					type: 'question-FR',
					label: "Help",
					value: "help"
				}]
			}],
		};
	},
	onBack: function onNext() {
		this.setState({
			current_page: Math.max(this.state.current_page-1, 0),
		});
	},
	onNext: function onNext() {
		this.setState({
			current_page: Math.min(this.state.max, this.state.current_page+1),
		});
	},
	render: function renderQuizBody() {
		return h('div', {
		}, [
			"HELLO ", this.props.quiz,
			h('div', [
				h(MaterialUI.FlatButton, {
					label: "Back",
					onTouchTap: this.onBack,
					style: {marginLeft:'12px'},
					disabled: !this.state.current_page,
				}),
				h(MaterialUI.SelectField, {
					value: this.state.current_page,
					onChange: (event, value) => this.setState({current_page:value}),
				}, this.state.pages.map(({name}, index) => h(MaterialUI.MenuItem, {
					value: index, primaryText: name,
				}))),
				h(MaterialUI.RaisedButton, {
					label: this.state.current_page === this.state.max ? "Finish" : "Next",
					primary: true,
					onTouchTap: this.onNext,
					style: {margin:'12px 0'},
				}),
			]),
			Page.h(this.state.pages[this.state.current_page]),
		]);
	},
});
