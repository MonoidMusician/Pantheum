var ReactDOM = require('react-dom');
var h = require('react-hyperscript');

var model = require('../model');
var user = require('../user');

module.exports = function(view) {
	view.pages['home'] = {
		title: 'Home | Pantheum',
		heading: 'Welcome',
		data(get, form) {
		},
		toJSON(word) {
		},
		fromJSON(json) {
		},
		render(word) {
			return view.Entry.h();
		},
	};
	view.pages['quiz'] = {
		title: 'Quiz | Pantheum',
		heading: 'Quiz',
		data(get, form) {
		},
		toJSON(word) {
		},
		fromJSON(json) {
		},
		render(word) {
			return view.Entry.h();
		},
	};
	view.pages['help'] = {
		title: 'Help | Pantheum',
		heading: 'Help',
		data(get, form) {
		},
		toJSON(word) {
		},
		fromJSON(json) {
		},
		render(word) {
			return view.Entry.h();
		},
	};
	view.pages['login'] = {
		title: 'Login | Pantheum',
		heading: 'Login',
		data(get, form) {
		},
		toJSON(word) {
		},
		fromJSON(json) {
		},
		render(word) {
			return view.Entry.h();
		},
	};
};
