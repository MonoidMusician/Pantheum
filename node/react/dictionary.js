var ReactDOM = require('react-dom');
var h = require('react-hyperscript');
var MaterialUI = require('material-ui');

var model = require('../model');
var user = require('../user');

var la_ipa = require('../lib/la_ipa');


module.exports = function(view) {
	view.pages['sum'] = {
		title: 'sum | Dictionary | Pantheum',
		heading: 'Dictionary entry for sum, esse, fui',
		data(get, form) {
			var word = {
				id: 10176,
				entry: "sum, esse, fui", // TODO: should be calculated from spart and forms (and attrs)
				attrs: {
					common:true,
					copulative:true,
					irregular:true,
					transitive:false
				},
			};
			view.word = word = model.Word(word, true);
			return word.pullall();
		},
		toJSON(word) {
			return word.toJSON();
		},
		fromJSON(json) {
			var word;
			view.word = word = model.Word({}, true).fromJSON(json);
			Object.assign(word, {
				entry: "sum, esse, fui", // TODO: should be calculated from spart and forms (and attrs)
				attrs: {
					common:true,
					copulative:true,
					irregular:true,
					transitive:false
				}
			});
			return word;
		},
		render(word, req) {
			var muiTheme = require('./style/muiTheme')(req);
			var UserProvider = require('./display/UserProvider');
			var Entry = require('./display/Entry');
			return h(MaterialUI.styles.MuiThemeProvider, {muiTheme}, h(UserProvider, {user:{administrator:true}},  Entry.h({word})));
		},
	};
};
