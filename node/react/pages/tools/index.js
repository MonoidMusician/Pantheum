var MaterialUI = require('material-ui');

var h = require('react-hyperscript');
var App = require('../../app');

var RomanNumerals = require('./roman-numerals');
var RomanTime = require('./roman-time');

module.exports = {
	title: 'Tools | Pantheum',
	data(get, form) {
	},
	render() {
		return App.h({page:'tools'}, [
			h('h1', 'Tools'),
			RomanNumerals.h(),
			RomanTime.h(),
		]);
	}
};
