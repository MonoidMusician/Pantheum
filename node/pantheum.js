window.stampit = require('stampit');
window.React = require('react');
window.ReactDOM = require('react-dom');
window.h = require('react-hyperscript');
window.la_ipa = require('./lib/la_ipa');
window.pantheum = {
	model: require('./model'),
	view: require('./react'),
	user: require('./user'),
	languages: require('./languages'),
};
window.la2en = require('./languages/la/translate').en;
window.MaterialUI = require('material-ui');
window.MaterialUI.svgicons = require('material-ui/svg-icons');
window.MaterialUI.styles = require('material-ui/styles');
