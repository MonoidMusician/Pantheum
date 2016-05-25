var ReactDOM = require('react-dom');
var h = require('react-hyperscript');

var model = require('../model');
var user = require('../user');

var MaterialUI = require('material-ui');
MaterialUI.styles = require('material-ui/styles');
MaterialUI.svgicons = require('material-ui/svg-icons');

var public_navigation = [
	{ value: "home", image: h(MaterialUI.svgicons.ActionHome), title: "Home" },
	{ value: "quiz", image: h(MaterialUI.svgicons.ActionClass), title: "Quiz" },
	{ value: "help",  image: h(MaterialUI.svgicons.ActionHelp), title: "Help" },
	{ value: "login", image: h(MaterialUI.svgicons.ActionFingerprint), title: "Login" },
	{ href: "/sum", value: "sum", image: h(MaterialUI.svgicons.ActionDescription), title: "Sum - Dictionary" },
	{ value: "dictionary", image: h(MaterialUI.svgicons.ActionDescription), title: "Dictionary" },
	{ href: "/CSS/react.css", title: "CSS" },
];

// TODO: move to module

var muiTheme = MaterialUI.styles.getMuiTheme({
	fontFamily: 'Linux Biolinum',
	palette: {
		primary1Color: '#CC3333',
		accent1Color:  '#DA7B00',
	},
});

module.exports = function(view) {
	var navigationWidth = 256;
	var App = view.createClass({
		render: function renderApp() {
			return h(MaterialUI.styles.MuiThemeProvider, {muiTheme}, [
				h("div", { style: { marginLeft: navigationWidth } }, [
					h(MaterialUI.AppBar, { title: "Latin", iconClassNameRight: "muidocs-icon-navigation-expand-more" }),
					view.Navigation.h({ pages: public_navigation, value: this.props.page }),
					h("div", {}, this.props.children)
				])
			]);
		},
	});

	view.pages['home'] = {
		title: 'Home | Pantheum',
		data(get, form) {
			return null;
		},
		render() {
			return App.h({page:'home'}, [
				h('h1#title', 'Welcome'),
				h('span', 'Welcome to the Pantheum, quizzes and a dictionary for Latin, created by Nick Scheel with website design by Alex Scheel.'),
				h('div#buttons', [
					h(MaterialUI.FlatButton, { href: '/quiz', linkButton:true }, 'Quiz')
				])
			]);
		},
	};
	view.pages['quiz'] = {
		title: 'Quiz | Pantheum',
		data() {

		},
		render() {
			return App.h({page:'quiz'}, h('div', 'Quiz! CONTENT GOES HERE'));
		},
	};
	view.pages['help'] = {
		title: 'Help | Pantheum',
		data() {

		},
		render() {
			return App.h({page:'help'}, h('div', 'Help! CONTENT GOES HERE'));
		},
	};
	view.pages['login'] = {
		title: 'Login | Pantheum',
		data() {

		},
		render() {
			return App.h({page:'login'}, h('div', 'Login! CONTENT GOES HERE'));
		},
	};
	function wordspart(word, spart) {
		return {value:word+' ('+spart+')', text: word};
	}
	view.DictionaryForm = view.createClass({
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
	})
	view.pages['dictionary'] = {
		title: 'Dictionary | Pantheum',
		data(get, form) {
		},
		render() {
			return App.h({page:'dictionary'}, [
				h('h1', 'Dictionary'),
				h('h2', 'Find words by name, attributes, language, and/or part of speech'),
				view.DictionaryForm.h()
			]);
		}
	};
};
