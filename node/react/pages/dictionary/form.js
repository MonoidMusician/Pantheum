var h = require('react-hyperscript');
var MaterialUI = require('material-ui');

var createClass = require('../../createClass');

function wordspart(word, spart) {
	return {value:word+' ('+spart+')', text: word};
}
module.exports = createClass({
	getInitialState: function() {
		return {
			searching: false
		};
	},
	handleSearch: function() {
		this.setState({searching:true});
	},
	render() {
		var children = [
			h(MaterialUI.Checkbox, {label: 'Hide inflection'}),
			h(MaterialUI.Checkbox, {label: 'Show declensions/conjugations'}),
			h('div', {
				style: {
					display: 'inline-div',
				}
			}, [
				h(MaterialUI.SelectField, {
					floatingLabelText: 'Language',
					style: {
						// https://github.com/callemall/material-ui/issues/2601
						verticalAlign: 'bottom'
					}
					// https://github.com/callemall/material-ui/pull/4252
				}, [
					h(MaterialUI.MenuItem, {value:'la', primaryText:'Latin'}),
					//h(MaterialUI.MenuItem, {value:'en', primaryText:'English'}),
				]),
				h(MaterialUI.AutoComplete, {
					floatingLabelText: 'Name(s)',
					hintText: 'Enter the name of any word',
					dataSource: [
						wordspart('sum', 'verb'),
						wordspart('ego', 'pronoun'),
					],
				}),
				h(MaterialUI.AutoComplete, {
					floatingLabelText: 'Form(s)',
					hintText: 'Enter any form of any word',
					dataSource: [
						wordspart('sum', 'verb'),
						wordspart('es', 'verb'),
					],
				}),
				h(MaterialUI.RaisedButton, {
					label: 'Search',
					primary: true,
					onTouchTap: this.handleSearch,
				}),
			])
		];
		if (this.state.searching) children.push(h(MaterialUI.CircularProgress));
		return h('div#form', children);
	},
});
