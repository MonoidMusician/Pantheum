var model = {};
module.exports = model;

[
	'depath',
	'form',
	'definition',
	'word',
].forEach(f => require('./'+f)(model));
