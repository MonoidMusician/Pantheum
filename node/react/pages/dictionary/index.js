var DictionaryForm = require('./form');
var App = require('../../app');

module.exports = {
	title: 'Dictionary | Pantheum',
	data(get, form) {
	},
	render() {
		return App.h({page:'dictionary'}, [
			h('h1', 'Dictionary'),
			h('h2', 'Find words by name, attributes, language, and/or part of speech'),
			DictionaryForm.h()
		]);
	}
};
