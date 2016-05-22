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
	{ href: "/CSS/react.css", title: "CSS" },
];

module.exports = function(view) {
	var navigationWidth = 256;
	var App = view.createClass({
		render: function renderApp() {
			return h(MaterialUI.styles.MuiThemeProvider, {muiTheme:MaterialUI.styles.getMuiTheme()}, [
				h("div", { style: { marginLeft: navigationWidth } }, [
					h(MaterialUI.AppBar, { title: "Latin", iconClassNameRight: "muidocs-icon-navigation-expand-more", style: {backgroundColor:'#CC3333'} }),
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
			return App.h({page:'home'}, h('div', 'Welcome! CONTENT GOES HERE'));
		},
	};
	view.pages['quiz'] = {
		title: 'Quiz | Pantheum',
		heading: 'Quiz',
		data() {

		},
		render() {
			return App.h({page:'quiz'}, h('div', 'Quiz! CONTENT GOES HERE'));
		},
	};
	view.pages['help'] = {
		title: 'Help | Pantheum',
		heading: 'Help',
		data() {

		},
		render() {
			return App.h({page:'help'}, h('div', 'Help! CONTENT GOES HERE'));
		},
	};
	view.pages['login'] = {
		title: 'Login | Pantheum',
		heading: 'Login',
		data() {

		},
		render() {
			return App.h({page:'login'}, h('div', 'Login! CONTENT GOES HERE'));
		},
	};
};
