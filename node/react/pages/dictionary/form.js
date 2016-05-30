var h = require('react-hyperscript');
var MaterialUI = require('material-ui');

var model = require('../../../model');

var Entry = require('../../display/Entry');

var createClass = require('../../createClass');

function wordspart(word, spart) {
	return {value:word+' ('+spart+')', text: word};
}
module.exports = createClass({
	getInitialState: function() {
		return {
			language: null,
			searching: false,
			words: [],
		};
	},
	handleLanguage: function(event, index, language) {
		this.setState({language});
	},
	handleSearch: function() {
		this.setState({searching:true});
		model.Word({id:10176}).pullall().then(word => {
			this.handleData([word])
		});
	},
	handleData: function(words) {
		this.setState({searching:false, words});
	},
	render() {
		var children = [
			h(MaterialUI.Checkbox, {label: "Show non-words"}),
			h('div', {
				style: {
					display: 'inline-div',
				}
			}, [
				h(MaterialUI.SelectField, {
					floatingLabelText: "Language",
					value: this.state.language,
					onChange: this.handleLanguage,
					style: {
						// https://github.com/callemall/material-ui/issues/2601
						verticalAlign: 'bottom'
					}
					// https://github.com/callemall/material-ui/pull/4252
				}, [
					h(MaterialUI.MenuItem, {value:'la', primaryText:"Latin"}),
					//h(MaterialUI.MenuItem, {value:'en', primaryText:'English'}),
				]),
				h(MaterialUI.AutoComplete, {
					floatingLabelText: "Name(s)",
					hintText: "Enter the name of any word",
					dataSource: [
						wordspart('sum', 'verb'),
						wordspart('ego', 'pronoun'),
					],
				}),
				h(MaterialUI.AutoComplete, {
					floatingLabelText: "Form(s)",
					hintText: "Enter any form of any word",
					dataSource: [
						wordspart('sum', 'verb'),
						wordspart('es', 'verb'),
					],
				}),
				h(MaterialUI.RaisedButton, {
					label: "Search",
					primary: true,
					onTouchTap: this.handleSearch,
				}),
			])
		];
		var makeentry = word => Entry.h({
			word,
			onRefresh: () => {
				this.setState({words: []});
				this.handleSearch();
			},
			onDelete: () => {
				this.setState({words: this.state.words.filter(w => w !== word)});
			},
		});
		if (this.state.searching) children.push(h(MaterialUI.LinearProgress));
		else children.push(...this.state.words.map(makeentry));
		return h('div#form', children);
	},
});
