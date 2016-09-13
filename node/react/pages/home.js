var MaterialUI = require('material-ui');

var h = require('react-hyperscript');
var App = require('../app');

module.exports = {
	title: 'Home | Pantheum',
	data(get, form) {
		return true;
	},
	render(data, req) {
		return App.h({page:'home',req}, [
			h('h1#title', 'Welcome'),
			h('span', 'Welcome to the Pantheum, quizzes and a dictionary for Latin, created by Nick Scheel.'),
			h('div#buttons', [
				h(MaterialUI.FlatButton, { href: '/quiz' }, 'Quiz')
			])
		]);
	},
};
