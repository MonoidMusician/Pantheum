window.la_ipa = require('./lib/la_ipa');
window.pantheum = {
	model: require('./model'),
	view: require('./react'),
	user: require('./user'),
	languages: require('./languages'),
};
window.la2en = require('./languages/la/translate').en;
