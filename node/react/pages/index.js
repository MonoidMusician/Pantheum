module.exports.quiz = require('./quiz');
module.exports.dictionary = require('./dictionary');
module.exports.home = require('./home');
module.exports.login = require('./account/login');
module.exports.tools = require('./tools');

var h = require('react-hyperscript');
var App = require('../app');

module.exports.help = {
	title: 'Help | Pantheum',
	data() {},
	render(data, req) {
		return App.h({page:'help',req}, h('div', 'Help! CONTENT GOES HERE'));
	},
};
