var h = require('react-hyperscript');
var MaterialUI = require('material-ui');
MaterialUI.svgicons = require('material-ui/svg-icons');

module.exports = [
	{ value: "home", image: h(MaterialUI.svgicons.ActionHome), title: "Home" },
	{ value: "quiz", image: h(MaterialUI.svgicons.ActionClass), title: "Quiz" },
	{ value: "help",  image: h(MaterialUI.svgicons.ActionHelp), title: "Help" },
	{ value: "login", image: h(MaterialUI.svgicons.ActionFingerprint), title: "Login" },
	{ href: "/sum", value: "sum", image: h(MaterialUI.svgicons.ActionDescription), title: "Sum - Dictionary" },
	{ value: "dictionary", image: h(MaterialUI.svgicons.ActionDescription), title: "Dictionary" },
	{ href: "/CSS/react.css", title: "CSS" },
];
